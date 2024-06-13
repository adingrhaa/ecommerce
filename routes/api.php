<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ProductCountController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CheckoutHistoryController;
use App\Http\Controllers\CheckoutInformationController;


// admin
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth' 
], function(){
    Route::post('admin', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    
    // Route::post('logout', [AuthController::class, 'logout']);
    // Route::post('login', [AuthController::class, 'login_member']); //ketika menggunakan api {nyalakan}
});

// Route::get('products', [ProductController::class, 'index'])->middleware(['auth:sanctum']);
// Route::get('products/{products}', [ProductController::class, 'show'])->middleware(['auth:sanctum']);

// auth member
Route::post('login', [AuthenticationController::class, 'login_member']);
Route::get('logout', [AuthenticationController::class, 'logout_member'])->middleware(['auth:sanctum', 'check.blocked']);
Route::get('me', [AuthenticationController::class, 'me'])->middleware(['auth:sanctum', 'check.blocked']);

//search 
Route::get('products/search',[ProductController::class,'search']);
Route::get('checkout/search', [CheckoutInformationController::class, 'search']);

// count
Route::get('count_products', [ProductCountController::class, 'countProducts']);

Route::delete('/carts/{id?}', [CartController::class, 'destroy']);

Route::group([
    'middleware' => 'api'
], function(){
    Route::resources([
        'categories' => CategoryController::class,
        'subcategories' => SubcategoryController::class,
        'sliders' => SliderController::class,
        'products' => ProductController::class,
        'testimonis' => TestimoniController::class,
        'members' => MemberController::class,
        'reviews' => ReviewController::class,
        'orders' => OrderController::class,
        'checkout_informations' => CheckoutInformationController::class,
        'carts' => CartController::class,
    ]);
});

// block unblock member
Route::middleware(['auth', 'check.blocked'])->group(function () {
    Route::post('/members/block-member/{id}', [MemberController::class, 'blockMember'])->name('members.blockMember');
    Route::post('/members/unblock-member/{id}', [MemberController::class, 'unblockMember'])->name('members.unblockMember');
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('checkout_histories', [CheckoutHistoryController::class, 'index']);
    Route::get('checkout_histories/{id}', [CheckoutHistoryController::class, 'show']);
    Route::post('checkout_histories', [CheckoutHistoryController::class, 'store']);
    Route::put('checkout_histories/{id}', [CheckoutHistoryController::class, 'update']);
    Route::delete('checkout_histories/{id}', [CheckoutHistoryController::class, 'destroy']);
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::put('checkout_informations/{id}', [CheckoutInformationController::class, 'update']);
});