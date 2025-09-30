<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PaymentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/payment/callback', [PaymentController::class, 'webhook']);

Route::middleware(['auth:sanctum', 'api'])->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('checkouts', CheckoutController::class);
    Route::post('/checkouts/{id}/cancel', [CheckoutController::class, 'cancel']);
    Route::post('/payment/{checkout}', [PaymentController::class, 'create']);
});

