<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserCart;
use App\Models\ProductColor;
use App\Models\UserAddress;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required|exists:user_addresses,id',
                'payment_method' => 'required|in:cash_on_delivery,online',
                'coupon_code' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            // Verify address belongs to user
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', $request->user_id)
                ->where('is_active', true)
                ->first();

            if (!$address) {
                return $this->toJsonEnc([], 'Invalid address selected', Config::get('constant.ERROR'));
            }

            // Get cart items
            $cartItems = UserCart::where('user_id', $request->user_id)
                ->with(['product', 'productColor'])
                ->get();

            if ($cartItems->isEmpty()) {
                return $this->toJsonEnc([], 'Cart is empty', Config::get('constant.ERROR'));
            }

            DB::beginTransaction();
            
            // Validate stock availability with lock (atomic check)
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product_color_id) {
                    $color = ProductColor::lockForUpdate()->find($cartItem->product_color_id);
                    if (!$color || $color->stock < $cartItem->quantity) {
                        DB::rollBack();
                        return $this->toJsonEnc([], 'Insufficient stock for ' . $cartItem->product->name, Config::get('constant.ERROR'));
                    }
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                $price = $item->product->discounted_price ?? $item->product->price;
                return $price * $item->quantity;
            });

            $discountAmount = 0;
            $coupon = null;

            // Apply coupon if provided
            if (!empty($request->coupon_code)) {
                $couponResult = $this->applyCoupon($request->coupon_code, $subtotal);
                if ($couponResult['success']) {
                    $coupon = $couponResult['coupon'];
                    $discountAmount = $couponResult['discount_amount'];
                } else {
                    DB::rollBack();
                    return $this->toJsonEnc([], $couponResult['message'], Config::get('constant.ERROR'));
                }
            }

            $shippingCharges = $this->calculateShippingCharges($subtotal - $discountAmount);
            $totalAmount = $subtotal - $discountAmount + $shippingCharges;

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $request->user_id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_charges' => $shippingCharges,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'address_id' => $request->address_id,
                'coupon_code' => $coupon ? $coupon->code : null,
                'coupon_discount' => $discountAmount,
                'estimated_delivery_date' => Carbon::now()->addDays(7),
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $unitPrice = $cartItem->product->discounted_price ?? $cartItem->product->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $request->user_id,
                    'seller_id' => $cartItem->product->user_id,
                    'product_id' => $cartItem->product_id,
                    'product_color_id' => $cartItem->product_color_id,
                    'product_title' => $cartItem->product->name,
                    'color_name' => $cartItem->productColor->color_name ?? null,
                    'color_value' => $cartItem->productColor->color_code ?? null,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $cartItem->quantity,
                    'item_status' => 'pending',
                ]);

                // Update stock atomically (already locked)
                if ($cartItem->product_color_id) {
                    $color = ProductColor::lockForUpdate()->find($cartItem->product_color_id);
                    if ($color) {
                        // Double-check stock availability before deducting
                        if ($color->stock < $cartItem->quantity) {
                            DB::rollBack();
                            return $this->toJsonEnc([], 'Insufficient stock for ' . $cartItem->product->name, Config::get('constant.ERROR'));
                        }
                        $color->decrement('stock', $cartItem->quantity);
                    }
                }
            }

            // Clear cart
            UserCart::where('user_id', $request->user_id)->delete();

            DB::commit();

            return $this->getOrderDetails($request, $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }

    public function getOrderDetails(Request $request, $orderId = null)
    {
        try {
            $orderId = $orderId ?? $request->order_id;

            $order = Order::with([
                'orderItems.product',
                'orderItems.productColor',
                'orderItems.seller',
                'address'
            ])->where('id', $orderId)
                ->where('user_id', $request->user_id)
                ->where('is_deleted', false)
                ->first();

            if (!$order) {
                return $this->toJsonEnc([], 'Order not found', Config::get('constant.NOT_FOUND'));
            }

            // Build order items array
            $orderItemsArray = $order->orderItems->map(function ($item) {
                $images = [];
                if ($item->product && !empty($item->product->images)) {
                    $images = is_array($item->product->images)
                        ? $item->product->images
                        : json_decode($item->product->images, true);
                }

                $productImageUrl = null;
                if ($images && count($images) > 0) {
                    $productImageUrl = asset('storage/products/' . $images[0]);
                }

                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_title' => $item->product_title,
                    'color_name' => $item->color_name,
                    'color_code' => $item->color_value,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'item_status' => $item->item_status,
                    'product_image' => $productImageUrl,
                    'seller_name' => $item->seller ? $item->seller->name : null,
                ];
            })->toArray();

            $orderData = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at,
                'estimated_delivery_date' => $order->estimated_delivery_date,
                'delivered_at' => $order->delivered_at,

                // Price breakdown
                'price_breakdown' => [
                    'subtotal' => $order->subtotal,
                    'discount' => $order->discount_amount,
                    'shipping_charges' => $order->shipping_charges,
                    'total_amount' => $order->total_amount,
                    'coupon_code' => $order->coupon_code,
                ],

                // Address details
                'shipping_address' => $order->address ? [
                    'name' => $order->address->full_name,
                    'block_number' => $order->address->block_number,
                    'building_name' => $order->address->building_name,
                    'area_street' => $order->address->area_street,
                    'state' => $order->address->state,
                ] : null,

                // Order items
                'order_items' => $orderItemsArray,
            ];

            return $this->toJsonEnc($orderData, 'Order details retrieved successfully', Config::get('constant.SUCCESS'));
        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }

    public function listOrders(Request $request)
    {
        try {
            $orders = Order::with(['orderItems.product', 'orderItems.seller'])
                ->where('user_id', $request->user_id)
                ->where('is_deleted', false)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($orders->isEmpty()) {
                return $this->toJsonEnc([], 'No orders found', Config::get('constant.ERROR'));
            }

            $ordersArray = $orders->map(function ($order) {
                $firstItem = $order->orderItems->first();
                $images = [];
                
                if ($firstItem && $firstItem->product && !empty($firstItem->product->images)) {
                    $images = is_array($firstItem->product->images)
                        ? $firstItem->product->images
                        : json_decode($firstItem->product->images, true);
                }

                $productImageUrl = null;
                if ($images && count($images) > 0) {
                    $productImageUrl = asset('storage/products/' . $images[0]);
                }

                return [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->orderItems->count(),
                    'created_at' => $order->created_at,
                    'estimated_delivery_date' => $order->estimated_delivery_date,
                    'product_image' => $productImageUrl,
                    'first_product_title' => $firstItem ? $firstItem->product_title : null,
                ];
            })->toArray();

            return $this->toJsonEnc($ordersArray, 'Orders retrieved successfully', Config::get('constant.SUCCESS'));
        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }

    private function generateOrderNumber()
    {
        return 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    private function calculateShippingCharges($amount)
    {
        return $amount > 500 ? 0 : 50;
    }

    private function applyCoupon($couponCode, $subtotal)
    {
        $coupon = Coupon::active()
            ->where('code', $couponCode)
            ->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code'];
        }

        if ($subtotal < $coupon->min_amount) {
            return [
                'success' => false,
                'message' => "Minimum order amount of ₹{$coupon->min_amount} required"
            ];
        }

        // Use the calculateDiscount method from Coupon model
        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'success' => true,
            'coupon' => $coupon,
            'discount_amount' => $discount
        ];
    }
}