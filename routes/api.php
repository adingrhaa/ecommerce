<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CheckoutInformationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductCountController;


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

Route::post('login', [AuthenticationController::class, 'login_member']);
Route::get('logout', [AuthenticationController::class, 'logout_member'])->middleware(['auth:sanctum']);
Route::get('me', [AuthenticationController::class, 'me'])->middleware(['auth:sanctum']);

Route::get('products/search',[ProductController::class,'search']);

Route::get('count_products', [ProductCountController::class, 'countProducts']);

Route::group([
    'middleware' => 'api'
], function(){
    Route::resources([
        'categories' => CategoryController::class,
        'subcategories' => SubcategoryController::class,
        'sliders' => SliderController::class,
        'products' => ProductController::class,
        'members' => MemberController::class,
        'testimonis' => TestimoniController::class,
        'reviews' => ReviewController::class,
        'orders' => OrderController::class,
        'checkout_informations' => CheckoutInformationController::class,
        'carts' => CartController::class,
    ]);

    Route::get('order/dikonfirmasi', [OrderController::class, 'dikonfirmasi']);
    Route::get('order/dikemas', [OrderController::class, 'dikemas']);
    Route::get('order/dikirim', [OrderController::class, 'dikirim']);
    Route::get('order/diterima', [OrderController::class, 'diterima']);
    Route::get('order/selesai', [OrderController::class, 'selesai']);
    Route::post('order/ubah_status/{order}', [OrderController::class, 'ubah_status']);

    Route::get('reports', [ReportController::class, 'index']);
});