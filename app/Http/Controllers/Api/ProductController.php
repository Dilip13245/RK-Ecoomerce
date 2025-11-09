<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductReview;
use App\Models\UserWishlist;
use App\Models\UserCart;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Verify user is a seller
            $user = \App\Models\User::find($request->user_id);
            if (!$user || $user->user_type !== 'seller') {
                return $this->toJsonEnc([], 'Only sellers can create products', Config::get('constant.ERROR'));
            }

            // Verify seller is active
            if ($user->status !== 'active') {
                return $this->toJsonEnc([], 'Seller account must be active', Config::get('constant.ERROR'));
            }

            // Verify seller is verified by admin
            if (!$user->is_verified) {
                if ($user->step_no == 4) {
                    return $this->toJsonEnc([], 'Admin is looking into your profile and shortly let you in. Please wait for admin verification to create products.', Config::get('constant.ERROR'));
                }
                return $this->toJsonEnc([], 'Seller account must be verified by admin. Please complete your registration.', Config::get('constant.ERROR'));
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'discounted_price' => 'nullable|numeric|min:0',
                'min_quantity' => 'required|integer|min:1',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'nullable|exists:sub_categories,id',
                'images' => 'required|array|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
                'specifications' => 'nullable|array',
                'colors' => 'required|array|min:1',
                'colors.*.color_name' => 'required|string|max:255',
                'colors.*.color_code' => 'nullable|string|max:7',
                'colors.*.stock' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            // Handle image uploads
            $imageNames = [];
            foreach ($request->file('images') as $image) {
                $imageName = FileHelper::uploadImage($image, 'products');
                if ($imageName) {
                    $imageNames[] = $imageName;
                }
            }

            $product = Product::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'price' => $request->price,
                'discounted_price' => $request->discounted_price,
                'min_quantity' => $request->min_quantity,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'images' => $imageNames,
                'specifications' => $request->specifications,
            ]);

            // Create color variants
            foreach ($request->colors as $color) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'color_name' => $color['color_name'],
                    'color_code' => $color['color_code'] ?? null,
                    'stock' => $color['stock'],
                ]);
            }

            $product->load('colors', 'category', 'subcategory');
            
            // Format images with full URLs
            $product->images = array_map(function($image) {
                return asset('storage/products/' . $image);
            }, $product->images);

            return $this->toJsonEnc($product, 'Product created successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function list(Request $request)
    {
        try {
            $query = Product::with(['colors', 'category', 'subcategory'])
                ->active();

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by subcategory
            if ($request->filled('subcategory_id')) {
                $query->where('subcategory_id', $request->subcategory_id);
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                      ->orWhere('description', 'LIKE', '%' . $search . '%');
                });
            }

            $products = $query->orderBy('created_at', 'desc')->get();

            if ($products->isEmpty()) {
                return $this->toJsonEnc([], 'No products found', Config::get('constant.NOT_FOUND'));
            }

            // Get user's wishlist and cart items
            $userId = $request->user_id;
            $wishlistProductIds = [];
            $cartProductIds = [];

            if ($userId) {
                $wishlistProductIds = UserWishlist::where('user_id', $userId)
                    ->where('is_added', true)
                    ->pluck('product_id')
                    ->toArray();

                $cartProductIds = UserCart::where('user_id', $userId)
                    ->pluck('product_id')
                    ->toArray();
            }

            $products->transform(function($product) use ($wishlistProductIds, $cartProductIds) {
                // Format images with full URLs
                $product->images = array_map(function($image) {
                    return asset('storage/products/' . $image);
                }, $product->images);

                // Add first image for listing
                $product->first_image = $product->images[0] ?? null;

                // Check if in wishlist/cart
                $product->is_in_wishlist = in_array($product->id, $wishlistProductIds);
                $product->is_in_cart = in_array($product->id, $cartProductIds);

                // Check stock availability
                $product->is_in_stock = $product->colors->where('stock', '>', 0)->count() > 0;

                return $product;
            });

            return $this->toJsonEnc($products, 'Products retrieved successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function detail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $product = Product::with(['colors', 'category', 'subcategory', 'reviews.user'])
                ->active()
                ->find($request->product_id);

            if (!$product) {
                return $this->toJsonEnc([], 'Product not found', Config::get('constant.NOT_FOUND'));
            }

            // Format images with full URLs
            $product->images = array_map(function($image) {
                return asset('storage/products/' . $image);
            }, $product->images);

            // Check if in wishlist/cart
            $userId = $request->user_id;
            if ($userId) {
                $product->is_in_wishlist = UserWishlist::where('user_id', $userId)
                    ->where('product_id', $product->id)
                    ->where('is_added', true)
                    ->exists();

                $product->is_in_cart = UserCart::where('user_id', $userId)
                    ->where('product_id', $product->id)
                    ->exists();
            } else {
                $product->is_in_wishlist = false;
                $product->is_in_cart = false;
            }

            // Check stock availability
            $product->is_in_stock = $product->colors->where('stock', '>', 0)->count() > 0;

            // Format reviews
            $product->reviews->transform(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user->name,
                    'rating' => $review->rating,
                    'review' => $review->review,
                    'created_at' => $review->created_at->format('M d, Y'),
                ];
            });

            return $this->toJsonEnc($product, 'Product details retrieved successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function addToWishlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            UserWishlist::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'product_id' => $request->product_id,
                ],
                ['is_added' => true]
            );

            return $this->toJsonEnc([], 'Product added to wishlist', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function removeFromWishlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            UserWishlist::where('user_id', $request->user_id)
                ->where('product_id', $request->product_id)
                ->update(['is_added' => false]);

            return $this->toJsonEnc([], 'Product removed from wishlist', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function addToCart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'product_color_id' => 'required|exists:product_colors,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            // Check product availability
            $product = Product::active()->find($request->product_id);
            if (!$product) {
                return $this->toJsonEnc([], 'Product not available', Config::get('constant.ERROR'));
            }

            // Check color stock
            $color = ProductColor::active()->find($request->product_color_id);
            if (!$color || $color->stock < $request->quantity) {
                return $this->toJsonEnc(
                    ['available_stock' => $color->stock ?? 0],
                    'Insufficient stock',
                    Config::get('constant.ERROR')
                );
            }

            // Check existing cart item
            $existingCart = UserCart::where('user_id', $request->user_id)
                ->where('product_id', $request->product_id)
                ->where('product_color_id', $request->product_color_id)
                ->first();

            if ($existingCart) {
                $newQuantity = $existingCart->quantity + $request->quantity;

                if ($newQuantity > $color->stock) {
                    return $this->toJsonEnc(
                        [
                            'available_stock' => $color->stock,
                            'current_quantity' => $existingCart->quantity
                        ],
                        'Cannot exceed available stock',
                        Config::get('constant.ERROR')
                    );
                }

                $existingCart->update(['quantity' => $newQuantity]);
            } else {
                UserCart::create([
                    'user_id' => $request->user_id,
                    'product_id' => $request->product_id,
                    'product_color_id' => $request->product_color_id,
                    'quantity' => $request->quantity,
                ]);
            }

            return $this->toJsonEnc([], 'Product added to cart', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function edit(Request $request)
    {
        try {
            // Verify user is a seller
            $user = \App\Models\User::find($request->user_id);
            if (!$user || $user->user_type !== 'seller') {
                return $this->toJsonEnc([], 'Only sellers can edit products', Config::get('constant.ERROR'));
            }

            // Verify seller is active
            if ($user->status !== 'active') {
                return $this->toJsonEnc([], 'Seller account must be active', Config::get('constant.ERROR'));
            }

            // Verify seller is verified by admin
            if (!$user->is_verified) {
                if ($user->step_no == 4) {
                    return $this->toJsonEnc([], 'Admin is looking into your profile and shortly let you in. Please wait for admin verification to edit products.', Config::get('constant.ERROR'));
                }
                return $this->toJsonEnc([], 'Seller account must be verified by admin. Please complete your registration.', Config::get('constant.ERROR'));
            }

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'discounted_price' => 'nullable|numeric|min:0',
                'min_quantity' => 'required|integer|min:1',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'nullable|exists:sub_categories,id',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
                'specifications' => 'nullable|array',
                'colors' => 'required|array|min:1',
                'colors.*.color_name' => 'required|string|max:255',
                'colors.*.color_code' => 'nullable|string|max:7',
                'colors.*.stock' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $product = Product::where('id', $request->product_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$product) {
                return $this->toJsonEnc([], 'Product not found or unauthorized', Config::get('constant.NOT_FOUND'));
            }

            $updateData = [
                'name' => $request->name,
                'price' => $request->price,
                'discounted_price' => $request->discounted_price,
                'min_quantity' => $request->min_quantity,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'specifications' => $request->specifications,
            ];

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $imageNames = [];
                foreach ($request->file('images') as $image) {
                    $imageName = FileHelper::uploadImage($image, 'products');
                    if ($imageName) {
                        $imageNames[] = $imageName;
                    }
                }
                // Merge with existing images
                $existingImages = $product->images ?? [];
                $updateData['images'] = array_merge($existingImages, $imageNames);
            }

            $product->update($updateData);

            // Update colors - delete old and create new
            ProductColor::where('product_id', $product->id)->delete();
            foreach ($request->colors as $color) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'color_name' => $color['color_name'],
                    'color_code' => $color['color_code'] ?? null,
                    'stock' => $color['stock'],
                ]);
            }

            $product->load('colors', 'category', 'subcategory');
            
            // Format images with full URLs
            $product->images = array_map(function($image) {
                return asset('storage/products/' . $image);
            }, $product->images);

            return $this->toJsonEnc($product, 'Product updated successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function getCart(Request $request)
    {
        try {
            $cartItems = UserCart::with(['product.user', 'productColor'])
                ->where('user_id', $request->user_id)
                ->get();

            if ($cartItems->isEmpty()) {
                return $this->toJsonEnc([], 'Cart is empty', Config::get('constant.NOT_FOUND'));
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function($item) {
                $itemPrice = $item->product->discounted_price ?? $item->product->price;
                return $itemPrice * $item->quantity;
            });

            $totalItems = $cartItems->sum('quantity');
            
            // Apply coupon if provided
            $discount = 0;
            $couponTitle = null;
            if ($request->filled('coupon_code')) {
                $coupon = \App\Models\Coupon::active()
                    ->where('code', $request->coupon_code)
                    ->first();
                    
                if ($coupon) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponTitle = $coupon->title;
                }
            }
            
            $shippingCharges = 150; // Fixed shipping charges
            $total = $subtotal - $discount + $shippingCharges;

            $cartItems->transform(function($item) {
                $product = $item->product;
                
                // Format product images
                $product->images = array_map(function($image) {
                    return asset('storage/products/' . $image);
                }, $product->images);
                
                $product->first_image = $product->images[0] ?? null;
                $product->seller_name = $product->user ? $product->user->name : null; // Get seller name from user relationship
                
                // Calculate total price for this item
                $itemPrice = $product->discounted_price ?? $product->price;
                $item->item_total = $itemPrice * $item->quantity;
                
                return $item;
            });

            // Get user's active address
            $address = \App\Models\UserAddress::where('user_id', $request->user_id)
                ->active()
                ->first();

            $cartData = [
                'cart_items' => $cartItems,
                'price_breakdown' => [
                    'subtotal' => $subtotal,
                    'total_items' => $totalItems,
                    'discount' => $discount,
                    'coupon_title' => $couponTitle,
                    'shipping_charges' => $shippingCharges,
                    'total' => $total,
                ],
                'shipping_address' => $address,
                'estimated_delivery' => now()->addDays(7)->format('d M Y')
            ];

            return $this->toJsonEnc($cartData, 'Cart retrieved successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function updateCartQuantity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|exists:user_cart,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $cartItem = UserCart::where('id', $request->cart_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$cartItem) {
                return $this->toJsonEnc([], 'Cart item not found', Config::get('constant.NOT_FOUND'));
            }

            // Check stock
            $color = ProductColor::find($cartItem->product_color_id);
            if ($color->stock < $request->quantity) {
                return $this->toJsonEnc([], 'Insufficient stock', Config::get('constant.ERROR'));
            }

            $cartItem->update(['quantity' => $request->quantity]);

            return $this->toJsonEnc([], 'Cart quantity updated successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function removeFromCart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|exists:user_cart,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $cartItem = UserCart::where('id', $request->cart_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$cartItem) {
                return $this->toJsonEnc([], 'Cart item not found', Config::get('constant.NOT_FOUND'));
            }

            $cartItem->delete();

            return $this->toJsonEnc([], 'Product removed from cart', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }

    public function clearCart(Request $request)
    {
        try {
            UserCart::where('user_id', $request->user_id)->delete();

            return $this->toJsonEnc([], 'Cart cleared successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }
}