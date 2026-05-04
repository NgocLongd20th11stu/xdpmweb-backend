<?php

use App\Http\Controllers\admin\AdminProfileController;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\OrderController as AdminOrderController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\CheckoutController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\front\ProductController as FrontProductController;
use App\Http\Controllers\front\ShippingControlller as FrontShippingController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Models\User;


Route::post('/admin/login', [AuthController::class,'authenticate']);
Route::get('get-latest-products', [FrontProductController::class,'latestProducts']);
Route::get('get-featured-products', [FrontProductController::class,'featuredProducts']);
Route::get('get-categories', [FrontProductController::class,'getCategories']);
Route::get('get-brands', [FrontProductController::class,'getBrands']);
Route::get('get-products', [FrontProductController::class,'getProducts']);
Route::get('get-product/{id}', [FrontProductController::class,'getProduct']);
Route::post('register', [AccountController::class,'register']);
Route::post('login', [AccountController::class,'authenticate']);
Route::get('get-shipping-user', [FrontShippingController::class,'getShipping']);



Route::group(['middleware' => ['auth:sanctum','checkUserRole']], function(){
    Route::post('save-order', [OrderController::class,'saveOrder']);
    Route::get('get-order-details/{id}', [AccountController::class,'getOrderDetails']);
    Route::get('get-orders', [AccountController::class,'getOrders']);
    Route::post('update-profile', [AccountController::class,'updateProfile']);
    Route::get('get-profile-details', [AccountController::class,'getAccountDetails']);

    Route::post('vnpay-payment', [CheckoutController::class,'vnpay']);
    Route::post('vnpay-verify', [CheckoutController::class, 'vnpayVerify']);
});
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



// ==================Admin Route=======================================



Route::group(['middleware' => ['auth:sanctum', 'checkAdminRole']], function(){
    // Route::get('categories',[CategoryController::class,'index']);
    // Route::get('categories/{id}',[CategoryController::class,'show']);
    // Route::put('categories/{id}',[CategoryController::class,'update']);
    // Route::delete('categories/{id}',[CategoryController::class,'destroy']);
    // Route::post('categories',[CategoryController::class,'store']);

    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::get('sizes', [SizeController::class,'index']);
    Route::resource('products', ProductController::class);
    Route::post('temp-images', [TempImageController::class,'store']);
    Route::post('save-product-image', [ProductController::class,'saveProductImage']);
    Route::get('change-product-default-image', [ProductController::class,'updateDefaultImage']);
    Route::delete('delete-product-image/{id}', [ProductController::class,'deleteProductImage']);
    
    Route::get('orders', [AdminOrderController::class,'index']);
    Route::get('orders/{id}', [AdminOrderController::class,'detail']);
    Route::post('update-order/{id}', [AdminOrderController::class,'updateOrder']);

    Route::get('get-shipping', [ShippingController::class,'getShipping']);
    Route::post('save-shipping', [ShippingController::class,'updateShipping']);


    Route::post('update-admin-profile', [AdminProfileController::class,'updateAdminProfile']);
    Route::get('get-admin-profile-details', [AdminProfileController::class,'getAdminDetails']);

    Route::get('admin-dashboard', [DashboardController::class, 'getStats']);

    Route::get('admin-get-all-customers', [CustomerController::class, 'getAllCustomers']);

    
});
