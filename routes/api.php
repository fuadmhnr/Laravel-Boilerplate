<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
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

Route::group(['namespace' => 'Api'], function() {
    Route::post('register', [UserController::class, 'signup']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('verify_token', [UserController::class, 'verifyToken']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::get('/products/query', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'create']);
    });
});