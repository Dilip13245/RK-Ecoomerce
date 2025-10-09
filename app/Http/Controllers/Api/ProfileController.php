<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BankDetail;
use App\Models\UserAddress;
use App\Models\HelpSupport;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            
            if (!$user) {
                return $this->toJsonEnc([], 'User not found', 404);
            }

            return $this->toJsonEnc($user, 'Profile retrieved successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'phone' => 'required|string|unique:users,phone,' . $request->user_id,
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::find($request->user_id);
            
            if (!$user) {
                return $this->toJsonEnc([], 'User not found', 404);
            }

            $profileImage = $user->profile_image;
            if ($request->hasFile('profile_image')) {
                $profileImage = FileHelper::uploadImage($request->file('profile_image'), 'profile');
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'profile_image' => $profileImage,
            ]);

            return $this->toJsonEnc($user, 'Profile updated successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::find($request->user_id);
            
            if (!$user || !Hash::check($request->current_password, $user->password)) {
                return $this->toJsonEnc([], 'Current password is incorrect', 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return $this->toJsonEnc([], 'Password changed successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function getBankDetails(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            
            if (!$user || $user->user_type !== 'seller') {
                return $this->toJsonEnc([], 'Only sellers can access bank details', 403);
            }

            $bankDetails = BankDetail::where('user_id', $request->user_id)
                ->where('is_deleted', false)
                ->get();

            return $this->toJsonEnc($bankDetails, 'Bank details retrieved successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function createBankDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_holder_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:20',
                'ifsc_code' => 'required|string|max:11',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::find($request->user_id);
            
            if (!$user || $user->user_type !== 'seller') {
                return $this->toJsonEnc([], 'Only sellers can create bank details', 403);
            }

            $bankDetail = BankDetail::create([
                'user_id' => $request->user_id,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'is_active' => true,
                'is_deleted' => false,
            ]);

            return $this->toJsonEnc($bankDetail, 'Bank details created successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function updateBankDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:bank_details,id',
                'account_holder_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:20',
                'ifsc_code' => 'required|string|max:11',
                'delete' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $user = User::find($request->user_id);
            
            if (!$user || $user->user_type !== 'seller') {
                return $this->toJsonEnc([], 'Only sellers can update bank details', 403);
            }

            $bankDetail = BankDetail::where('id', $request->id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$bankDetail) {
                return $this->toJsonEnc([], 'Bank details not found', 404);
            }

            if ($request->delete) {
                $bankDetail->update([
                    'is_active' => false,
                    'is_deleted' => true,
                ]);
                return $this->toJsonEnc([], 'Bank details deleted successfully', 200);
            }

            $bankDetail->update([
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'is_active' => true,
            ]);

            return $this->toJsonEnc($bankDetail, 'Bank details updated successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function addAddress(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'block_number' => 'required|string|max:255',
                'building_name' => 'nullable|string|max:255',
                'area_street' => 'required|string|max:255',
                'state' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $address = UserAddress::create([
                'user_id' => $request->user_id,
                'full_name' => trim($request->full_name),
                'block_number' => trim($request->block_number),
                'building_name' => $request->building_name ? trim($request->building_name) : null,
                'area_street' => trim($request->area_street),
                'state' => trim($request->state),
                'is_active' => true,
                'is_deleted' => false,
            ]);

            return $this->toJsonEnc($address, 'Address added successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function updateAddress(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:user_addresses,id',
                'full_name' => 'required|string|max:255',
                'block_number' => 'required|string|max:255',
                'building_name' => 'nullable|string|max:255',
                'area_street' => 'required|string|max:255',
                'state' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $address = UserAddress::where('id', $request->id)
                ->where('user_id', $request->user_id)
                ->where('is_deleted', false)
                ->first();

            if (!$address) {
                return $this->toJsonEnc([], 'Address not found', 404);
            }

            $address->update([
                'full_name' => trim($request->full_name),
                'block_number' => trim($request->block_number),
                'building_name' => $request->building_name ? trim($request->building_name) : null,
                'area_street' => trim($request->area_street),
                'state' => trim($request->state),
            ]);

            return $this->toJsonEnc($address, 'Address updated successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function deleteAddress(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required|exists:user_addresses,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', $request->user_id)
                ->where('is_deleted', false)
                ->first();

            if (!$address) {
                return $this->toJsonEnc([], 'Address not found', 404);
            }

            $address->update([
                'is_active' => false,
                'is_deleted' => true,
            ]);

            return $this->toJsonEnc([], 'Address deleted successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function listAddresses(Request $request)
    {
        try {
            $addresses = UserAddress::where('user_id', $request->user_id)
                ->active()
                ->get();

            if ($addresses->isEmpty()) {
                return $this->toJsonEnc([], 'No addresses found', 404);
            }

            return $this->toJsonEnc($addresses, 'Addresses retrieved successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function submitHelpSupport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $helpSupport = HelpSupport::create([
                'user_id' => $request->user_id,
                'name' => trim($request->name),
                'email' => trim($request->email),
                'message' => trim($request->message),
                'status' => 'pending',
            ]);

            return $this->toJsonEnc($helpSupport, 'Help & support request submitted successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }
}