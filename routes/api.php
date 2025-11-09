<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/send-otp', [\App\Http\Controllers\Api\AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [\App\Http\Controllers\Api\AuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    Route::post('/complete-step', [\App\Http\Controllers\Api\AuthController::class, 'completeStep'])->middleware(['tokencheck']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

// Public routes
Route::prefix('categories')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CategoryController::class, 'categoryList']);
    Route::get('/subcategories', [\App\Http\Controllers\Api\CategoryController::class, 'subCategoryList']);
});

// Protected routes
Route::prefix('user')->middleware(['tokencheck'])->group(function () {
    
    Route::prefix('profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'getProfile']);
        Route::post('/update', [\App\Http\Controllers\Api\ProfileController::class, 'updateProfile']);
        Route::post('/change-password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
    });
    
    Route::prefix('address')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'listAddresses']);
        Route::post('/add', [\App\Http\Controllers\Api\ProfileController::class, 'addAddress']);
        Route::post('/update', [\App\Http\Controllers\Api\ProfileController::class, 'updateAddress']);
        Route::post('/delete', [\App\Http\Controllers\Api\ProfileController::class, 'deleteAddress']);
    });
    
    Route::post('/help-support', [\App\Http\Controllers\Api\ProfileController::class, 'submitHelpSupport']);
    
    Route::prefix('bank')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'getBankDetails']);
        Route::post('/create', [\App\Http\Controllers\Api\ProfileController::class, 'createBankDetails']);
        Route::post('/update', [\App\Http\Controllers\Api\ProfileController::class, 'updateBankDetails']);
    });
    
    Route::prefix('products')->group(function () {
        Route::post('/create', [\App\Http\Controllers\Api\ProductController::class, 'create']);
        Route::post('/edit', [\App\Http\Controllers\Api\ProductController::class, 'edit']);
        Route::get('/list', [\App\Http\Controllers\Api\ProductController::class, 'list']);
        Route::get('/detail', [\App\Http\Controllers\Api\ProductController::class, 'detail']);
        Route::post('/wishlist/add', [\App\Http\Controllers\Api\ProductController::class, 'addToWishlist']);
        Route::post('/wishlist/remove', [\App\Http\Controllers\Api\ProductController::class, 'removeFromWishlist']);
        Route::post('/cart/add', [\App\Http\Controllers\Api\ProductController::class, 'addToCart']);
    });
    
    Route::prefix('cart')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProductController::class, 'getCart']);
        Route::post('/update-quantity', [\App\Http\Controllers\Api\ProductController::class, 'updateCartQuantity']);
        Route::post('/remove', [\App\Http\Controllers\Api\ProductController::class, 'removeFromCart']);
        Route::post('/clear', [\App\Http\Controllers\Api\ProductController::class, 'clearCart']);
    });
    
    Route::prefix('coupons')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CouponController::class, 'listCoupons']);
        Route::post('/validate', [\App\Http\Controllers\Api\CouponController::class, 'validateCoupon']);
    });
    
    Route::prefix('orders')->group(function () {
        Route::post('/create', [\App\Http\Controllers\Api\OrderController::class, 'createOrder']);
        Route::get('/list', [\App\Http\Controllers\Api\OrderController::class, 'listOrders']);
        Route::get('/details', [\App\Http\Controllers\Api\OrderController::class, 'getOrderDetails']);
    });
    
    Route::prefix('seller/orders')->group(function () {
        Route::get('/list', [\App\Http\Controllers\Api\SellerOrderController::class, 'listOrders']);
        Route::get('/details', [\App\Http\Controllers\Api\SellerOrderController::class, 'getOrderDetails']);
        Route::post('/update-status', [\App\Http\Controllers\Api\SellerOrderController::class, 'updateOrderStatus']);
    });
    
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\WishlistController::class, 'getWishlist']);
    });
    
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'listNotifications']);
        Route::post('/mark-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    });
});