<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('shop')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        // Route::resource('/products', ProductController::class);
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/show/{uuid}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/products/update/{uuid}', [ProductController::class, 'update']);
        Route::post('/products/{uuid}', [ProductController::class, 'destroy']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/transaction', [TransactionController::class, 'index']);
        Route::get('/transaction/show/{uuid}', [TransactionController::class, 'show']);
        Route::post('/transaction', [TransactionController::class, 'store']);
        Route::post('/transaction/update/{uuid}', [TransactionController::class, 'update']);
        Route::post('/transaction/{uuid}', [TransactionController::class, 'destroy']);
    });
});