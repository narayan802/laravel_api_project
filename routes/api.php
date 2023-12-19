<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ProductrController;




Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);



//Protected Routes
// Route::group([
//     "middleware"=>["auth:api"]
// ],function(){

// });
Route::middleware('auth:api')->group(function () {
    Route::get('profile', [ApiController::class, 'profile']);
    Route::get("logout", [ApiController::class, "logout"]);

    Route::get('product', [ProductrController::class, 'show']);
    Route::post('product/insert', [ProductrController::class, 'insert']);
    Route::get('product/{id}', [ProductrController::class, 'singel_data']);
    Route::post('product/update/{id}', [ProductrController::class, 'update_data']);
    Route::delete('product/delete/{id}', [ProductrController::class, 'destroy']);
});
