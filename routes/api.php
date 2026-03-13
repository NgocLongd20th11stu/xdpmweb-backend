<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\TempImageController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Models\User;


Route::post('/admin/login', [AuthController::class,'authenticate']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// API hiển thị users
Route::get('/users', function () {
    return User::all();
});
Route::get('/add-user', function () {

    User::create([
        'name' => 'Admin',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('123456')
    ]);

    return "User created";
});



Route::group(['middleware' => 'auth:sanctum'], function(){
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

    
    
});
