<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\UserWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class WishlistController extends Controller
{
    public function getWishlist(Request $request)
    {
        try {
            $wishlistItems = UserWishlist::where('user_id', $request->user_id)
                ->where('is_added', true)
                ->pluck('product_id')
                ->toArray();

            if (empty($wishlistItems)) {
                return $this->toJsonEnc([], 'Wishlist is empty', Config::get('constant.NOT_FOUND'));
            }

            $products = Product::with(['colors', 'category', 'subcategory'])
                ->whereIn('id', $wishlistItems)
                ->active()
                ->get();

            if ($products->isEmpty()) {
                return $this->toJsonEnc([], 'No products found', Config::get('constant.NOT_FOUND'));
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

            return $this->toJsonEnc($products, 'Wishlist retrieved successfully', Config::get('constant.SUCCESS'));

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), Config::get('constant.INTERNAL_ERROR'));
        }
    }
}