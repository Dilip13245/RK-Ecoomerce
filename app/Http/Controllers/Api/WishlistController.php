<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\UserWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function getWishlist(Request $request)
    {
        try {
            $productIds = UserWishlist::where('user_id', $request->user_id)
                ->where('is_added', true)
                ->pluck('product_id')
                ->toArray();

            if (empty($productIds)) {
                return $this->toJsonEnc([], 'Wishlist is empty', 404);
            }

            $products = Product::with(['colors', 'category', 'subcategory'])
                ->whereIn('id', $productIds)
                ->active()
                ->get();

            if ($products->isEmpty()) {
                return $this->toJsonEnc([], 'No products found', 404);
            }

            $products->transform(function($product) {
                // Format images with full URLs
                $product->images = array_map(function($image) {
                    return asset('storage/products/' . $image);
                }, $product->images);

                $product->first_image = $product->images[0] ?? null;
                $product->is_in_wishlist = true;
                $product->is_in_stock = $product->colors->where('stock', '>', 0)->count() > 0;

                return $product;
            });

            return $this->toJsonEnc($products, 'Wishlist retrieved successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }
}