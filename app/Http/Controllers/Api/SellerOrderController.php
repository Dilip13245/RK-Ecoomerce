<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class SellerOrderController extends Controller
{
    public function listOrders(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status_filter' => 'nullable|string|in:pending,all',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $query = OrderItem::where('seller_id', $request->user_id)
                ->with(['order.user', 'product']);

            // Filter by status if requested
            if ($request->filled('status_filter') && $request->status_filter === 'pending') {
                $query->where('item_status', 'pending');
            }

            $orderItems = $query->orderBy('created_at', 'desc')->get();

            if ($orderItems->isEmpty()) {
                return $this->toJsonEnc([], 'No orders found', Config::get('constant.ERROR'));
            }

            $ordersArray = $orderItems->map(function ($item) {
                $images = [];
                if ($item->product && !empty($item->product->images)) {
                    $images = is_array($item->product->images)
                        ? $item->product->images
                        : json_decode($item->product->images, true);
                }

                $productImageUrl = null;
                if ($images && count($images) > 0) {
                    $productImageUrl = asset('uploads/products/' . $images[0]);
                }

                return [
                    'order_id' => $item->order_id,
                    'item_id' => $item->id,
                    'order_number' => $item->order->order_number,
                    'customer_name' => trim($item->order->user->first_name . ' ' . $item->order->user->last_name),
                    'product_title' => $item->product_title,
                    'product_image' => $productImageUrl,
                    'color_name' => $item->color_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'item_status' => $item->item_status,
                    'created_at' => $item->created_at,
                    'payment_method' => $item->order->payment_method,
                    'can_confirm' => $item->item_status === 'pending',
                    'can_ship' => $item->item_status === 'confirmed',
                    'can_deliver' => $item->item_status === 'shipped',
                ];
            })->toArray();

            return $this->toJsonEnc($ordersArray, 'Orders retrieved successfully', Config::get('constant.SUCCESS'));
        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }

    public function getOrderDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:order_items,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $orderItem = OrderItem::where('id', $request->item_id)
                ->where('seller_id', $request->user_id)
                ->with(['order.user', 'order.address', 'product'])
                ->first();

            if (!$orderItem) {
                return $this->toJsonEnc([], 'Order not found', Config::get('constant.NOT_FOUND'));
            }

            $images = [];
            if ($orderItem->product && !empty($orderItem->product->images)) {
                $images = is_array($orderItem->product->images)
                    ? $orderItem->product->images
                    : json_decode($orderItem->product->images, true);
            }

            $productImageUrl = null;
            if ($images && count($images) > 0) {
                $productImageUrl = asset('uploads/products/' . $images[0]);
            }

            $orderData = [
                'order_id' => $orderItem->order_id,
                'item_id' => $orderItem->id,
                'order_number' => $orderItem->order->order_number,
                'customer_name' => trim($orderItem->order->user->first_name . ' ' . $orderItem->order->user->last_name),
                'customer_phone' => $orderItem->order->user->phone,
                'product_title' => $orderItem->product_title,
                'product_image' => $productImageUrl,
                'color_name' => $orderItem->color_name,
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->unit_price,
                'total_price' => $orderItem->total_price,
                'item_status' => $orderItem->item_status,
                'payment_method' => $orderItem->order->payment_method,
                'created_at' => $orderItem->created_at,
                'shipping_address' => [
                    'name' => trim($orderItem->order->address->first_name . ' ' . $orderItem->order->address->last_name),
                    'phone' => $orderItem->order->address->phone,
                    'address' => $orderItem->order->address->address_line,
                    'city' => $orderItem->order->address->city,
                    'postal_code' => $orderItem->order->address->postal_code,
                ],
            ];

            return $this->toJsonEnc($orderData, 'Order details retrieved successfully', Config::get('constant.SUCCESS'));
        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }

    public function updateOrderStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:order_items,id',
                'status' => 'required|in:confirmed,shipped,delivered',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $orderItem = OrderItem::where('id', $request->item_id)
                ->where('seller_id', $request->user_id)
                ->first();

            if (!$orderItem) {
                return $this->toJsonEnc([], 'Order not found', Config::get('constant.NOT_FOUND'));
            }

            $orderItem->item_status = $request->status;
            if ($request->status === 'delivered') {
                $orderItem->order->delivered_at = now();
                $orderItem->order->save();
            }
            $orderItem->save();

            return $this->toJsonEnc(['status' => $request->status], 'Order status updated successfully', Config::get('constant.SUCCESS'));
        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.ERROR'));
        }
    }
}