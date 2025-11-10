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
            $existingUser = User::where('email', $request->email)->first();

            if ($existingUser) {
                // Check if user is fully registered
                $isFullyRegistered = false;
                
                if ($existingUser->user_type === 'customer' && $existingUser->step_no == 0 && $existingUser->is_verified) {
                    $isFullyRegistered = true;
                }
                
                if ($existingUser->user_type === 'seller' && $existingUser->step_no == 4 && $existingUser->is_verified) {
                    $isFullyRegistered = true;
                }
                
                if ($isFullyRegistered) {
                    return $this->toJsonEnc([
                        'user_type' => $existingUser->user_type
                    ], 'User already registered with ' . $existingUser->user_type . ' account. Please login to continue.', Config::get('constant.ERROR'));
                }
                
                // Delete incomplete registration (allow role change)
                UserDevice::where('user_id', $existingUser->id)->delete();
                $existingUser->delete();
                $existingUser = null;
            }

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'password' => 'required|string|min:6',
                'user_type' => 'required|in:customer,seller',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'device_type' => 'required|in:A,I',
            ];

            // Since we delete incomplete users above, always check uniqueness
            $rules['email'] = 'required|email|unique:users,email';
            if ($request->phone) {
                $rules['phone'] = 'nullable|string|unique:users,phone';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $profileImage = null;
            if ($request->hasFile('profile_image')) {
                $profileImage = FileHelper::uploadImage($request->file('profile_image'), 'profile');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? null,
                'profile_image' => $profileImage,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'status' => $request->user_type === 'seller' ? 'inactive' : 'active',
                'is_verified' => false,
                'step_no' => 1,
            ]);

            $otp = '1234';
            $expiresAt = now()->addMinutes(5);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $expiresAt
            ]);

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

            return $this->toJsonEnc([
                'user' => $user,
                'step_no' => $user->step_no,
                'otp' => $otp,
                'message' => 'OTP sent to your email. Please verify to continue.'
            ], 'OTP sent to your email.', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
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
                ->where('status', '!=', 'suspended')
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->toJsonEnc([], 'Invalid credentials', Config::get('constant.UNAUTHORIZED'));
            }

            if ($user->user_type === 'customer' && $user->step_no == 1 && !$user->is_verified) {
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
                
                return $this->toJsonEnc([
                    'user' => $user,
                    'step_no' => $user->step_no,
                    'registration_incomplete' => true,
                    'message' => 'Please verify your email OTP to complete registration.'
                ], 'Login successful. Please verify OTP to complete registration.', Config::get('constant.SUCCESS'));
            }

            if ($user->user_type === 'seller' && $user->step_no < 4) {
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
                
                return $this->toJsonEnc([
                    'user' => $user,
                    'step_no' => $user->step_no,
                    'registration_incomplete' => true,
                    'message' => 'Please complete your registration.'
                ], 'Login successful. Please complete registration.', Config::get('constant.SUCCESS'));
            }

            // Seller with step_no = 4 but not verified by admin
            if ($user->user_type === 'seller' && $user->step_no == 4 && !$user->is_verified) {
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
                
                return $this->toJsonEnc([
                    'user' => $user,
                    'step_no' => $user->step_no,
                    'is_verified' => false,
                    'admin_verification_pending' => true,
                    'message' => 'Admin is looking into your profile and shortly let you in.'
                ], 'Admin is reviewing your profile. You will be notified once approved.', Config::get('constant.SUCCESS'));
            }

            // Customer or verified seller - normal login
            if ($user->user_type === 'customer' && !$user->is_verified) {
                return $this->toJsonEnc([], 'Account not verified. Please verify your account.', Config::get('constant.UNAUTHORIZED'));
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

            return $this->toJsonEnc($user, 'Login successful', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function sendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'type' => 'required|in:register,forgot',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $otp = '1234';
            $expiresAt = now()->addMinutes(5);

            if ($request->type === 'register') {
                $user = User::where('email', $request->email)->first();
            } else {
                $user = User::where('email', $request->email)->where('status', 'active')->first();
            }
            
            if (!$user) {
                return $this->toJsonEnc([], 'User not found', Config::get('constant.NOT_FOUND'));
            }

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $expiresAt
            ]);

            return $this->toJsonEnc(['otp' => $otp], 'OTP sent successfully to your email', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|string',
                'device_type' => 'required|in:A,I',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('otp_expires_at', '>', now())
                ->first();

            if (!$user) {
                return $this->toJsonEnc([], 'Invalid or expired OTP', Config::get('constant.ERROR'));
            }

            if ($user->user_type === 'seller' && $user->step_no == 1) {
                $user->update([
                    'email_verified_at' => now(),
                    'otp' => null,
                    'otp_expires_at' => null,
                    'step_no' => 2,
                    'is_verified' => false
                ]);

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

                return $this->toJsonEnc([
                    'user' => $user,
                    'step_no' => $user->step_no,
                    'message' => 'OTP verified. Please upload your government ID.'
                ], 'OTP verified successfully. Please upload your government ID.', Config::get('constant.SUCCESS'));
            } else if ($user->user_type === 'customer' && $user->step_no == 1) {
                $user->update([
                    'email_verified_at' => now(),
                    'is_verified' => true,
                    'otp' => null,
                    'otp_expires_at' => null,
                    'step_no' => 0
                ]);

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

                return $this->toJsonEnc($user, 'OTP verified successfully. Registration completed.', Config::get('constant.SUCCESS'));
            } else {
                $user->update([
                    'email_verified_at' => now(),
                    'is_verified' => true,
                    'otp' => null,
                    'otp_expires_at' => null
                ]);

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

                return $this->toJsonEnc($user, 'OTP verified successfully. You can now login.', Config::get('constant.SUCCESS'));
            }

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('otp_expires_at', '>', now())
                ->first();

            if (!$user) {
                return $this->toJsonEnc([], 'Invalid or expired OTP', Config::get('constant.ERROR'));
            }

            $user->update([
                'password' => Hash::make($request->new_password),
                'otp' => null,
                'otp_expires_at' => null
            ]);

            return $this->toJsonEnc([], 'Password reset successfully. You can now login with your new password.', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function completeStep(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::where('id', $request->user_id)
                ->where('user_type', 'seller')
                ->where('status', '!=', 'suspended')
                ->first();

            if (!$user) {
                return $this->toJsonEnc([], 'User not found or inactive', Config::get('constant.NOT_FOUND'));
            }

            if ($user->step_no == 2) {
                $validator = Validator::make($request->all(), [
                    'government_id' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ]);

                if ($validator->fails()) {
                    return $this->validateResponse($validator->errors());
                }

                $governmentId = FileHelper::uploadImage($request->file('government_id'), 'documents');

                $user->update([
                    'government_id' => $governmentId,
                    'step_no' => 3
                ]);

                return $this->toJsonEnc([
                    'user_id' => $user->id,
                    'step_no' => $user->step_no,
                    'message' => 'Government ID uploaded. Please add your bank details.'
                ], 'Government ID uploaded successfully. Please add your bank details.', Config::get('constant.SUCCESS'));

            } elseif ($user->step_no == 3) {
                $validator = Validator::make($request->all(), [
                    'account_holder_name' => 'required|string|max:255',
                    'account_number' => 'required|string|max:20',
                    'ifsc_code' => 'required|string|max:11',
                ]);

                if ($validator->fails()) {
                    return $this->validateResponse($validator->errors());
                }

                BankDetail::create([
                    'user_id' => $user->id,
                    'account_holder_name' => $request->account_holder_name,
                    'account_number' => $request->account_number,
                    'ifsc_code' => $request->ifsc_code,
                    'is_active' => true,
                    'is_deleted' => false,
                ]);

                $user->update([
                    'step_no' => 4,
                    'status' => 'active',
                    'is_verified' => false,
                ]);

                return $this->toJsonEnc([
                    'user' => $user,
                    'step_no' => $user->step_no,
                    'message' => 'Registration completed successfully! Admin is looking into your profile and shortly let you in.'
                ], 'Registration completed successfully! Admin is reviewing your profile.', Config::get('constant.SUCCESS'));

            } else {
                return $this->toJsonEnc([], 'Invalid step. Please complete previous steps.', Config::get('constant.ERROR'));
            }

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
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

            return $this->toJsonEnc([], 'Logout successful', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }


}