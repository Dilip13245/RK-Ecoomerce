<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\BankDetail;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|unique:users,phone',
                'password' => 'required|string|min:6',
                'user_type' => 'required|in:customer,seller',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ];

            // Add seller-specific validation
            if ($request->user_type === 'seller') {
                $rules['government_id'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
                $rules['account_holder_name'] = 'required|string|max:255';
                $rules['account_number'] = 'required|string|max:20';
                $rules['ifsc_code'] = 'required|string|max:11';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            // Handle file uploads
            $profileImage = null;
            $governmentId = null;

            if ($request->hasFile('profile_image')) {
                $profileImage = FileHelper::uploadImage($request->file('profile_image'), 'profile');
            }

            if ($request->hasFile('government_id')) {
                $governmentId = FileHelper::uploadImage($request->file('government_id'), 'documents');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'profile_image' => $profileImage,
                'government_id' => $governmentId,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'status' => 'active',
                'is_verified' => false,
            ]);

            // Create bank details for seller
            if ($request->user_type === 'seller') {
                BankDetail::create([
                    'user_id' => $user->id,
                    'account_holder_name' => $request->account_holder_name,
                    'account_number' => $request->account_number,
                    'ifsc_code' => $request->ifsc_code,
                ]);
            }

            return $this->toJsonEnc($user, 'Registration successful. Please verify your phone number.', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'device_type' => 'required|in:A,I',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::where('email', $request->email)
                ->where('status', 'active')
                ->where('is_verified', true)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->toJsonEnc([], 'Invalid credentials or account not verified', 401);
            }

            $accessToken = Str::random(64);

            UserDevice::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'token' => $accessToken,
                    'device_type' => $request->device_type,
                    'ip_address' => $request->ip(),
                    'uuid' => $request->uuid ?? '',
                    'os_version' => $request->os_version ?? '',
                    'device_model' => $request->device_model ?? '',
                    'app_version' => $request->app_version ?? 'v1',
                    'device_token' => $request->device_token ?? '',
                ]
            );

            $user->token = $accessToken;

            return $this->toJsonEnc($user, 'Login successful', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function sendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'type' => 'required|in:register,forgot',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $otp = rand(1000, 9999);
            $expiresAt = now()->addMinutes(5);

            $user = User::where('phone', $request->phone)->where('status', 'active')->first();
            
            if (!$user) {
                return $this->toJsonEnc([], 'User not found', 404);
            }

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $expiresAt
            ]);

            return $this->toJsonEnc(['otp' => $otp], 'OTP sent successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'otp' => 'required|string',
                'device_type' => 'required|in:A,I',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::where('phone', $request->phone)
                ->where('otp', $request->otp)
                ->where('otp_expires_at', '>', now())
                ->first();

            if (!$user) {
                return $this->toJsonEnc([], 'Invalid or expired OTP', 400);
            }

            $user->update([
                'phone_verified_at' => now(),
                'is_verified' => true,
                'otp' => null,
                'otp_expires_at' => null
            ]);

            // Generate token after verification
            $accessToken = Str::random(64);

            UserDevice::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'token' => $accessToken,
                    'device_type' => $request->device_type,
                    'ip_address' => $request->ip(),
                    'uuid' => $request->uuid ?? '',
                    'os_version' => $request->os_version ?? '',
                    'device_model' => $request->device_model ?? '',
                    'app_version' => $request->app_version ?? 'v1',
                    'device_token' => $request->device_token ?? '',
                ]
            );

            $user->token = $accessToken;

            return $this->toJsonEnc($user, 'OTP verified successfully. You can now login.', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            
            UserDevice::where('user_id', $user_id)->update([
                'token' => '',
                'device_token' => ''
            ]);

            return $this->toJsonEnc([], 'Logout successful', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }


}