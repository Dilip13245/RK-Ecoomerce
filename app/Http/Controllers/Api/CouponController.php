<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function validateCoupon(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'coupon_code' => 'required|string',
                'cart_amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $coupon = Coupon::active()
                ->where('code', $request->coupon_code)
                ->first();

            if (!$coupon) {
                return $this->toJsonEnc([], 'Invalid coupon code', 400);
            }

            if ($request->cart_amount < $coupon->min_amount) {
                return $this->toJsonEnc(
                    ['min_amount' => $coupon->min_amount],
                    "Minimum cart amount should be ₹{$coupon->min_amount}",
                    400
                );
            }

            $discount = $coupon->calculateDiscount($request->cart_amount);

            return $this->toJsonEnc([
                'coupon' => [
                    'code' => $coupon->code,
                    'title' => $coupon->title,
                    'discount' => $discount,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                ]
            ], 'Coupon applied successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function listCoupons(Request $request)
    {
        try {
            $coupons = Coupon::active()
                ->select('code', 'title', 'type', 'value', 'min_amount', 'max_discount', 'valid_until')
                ->get();

            return $this->toJsonEnc($coupons, 'Coupons retrieved successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }
}