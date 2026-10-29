<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'cart-service',
    ]);
});

Route::get('/carts/{userId}', [CartController::class, 'show'])
    ->whereNumber('userId');

Route::post('/carts', [CartController::class, 'store']);

Route::put('/carts/items/{id}', [CartController::class, 'update'])
    ->whereNumber('id');

Route::delete('/carts/items/{id}', [CartController::class, 'destroy'])
    ->whereNumber('id');

Route::delete('/carts/{userId}', [CartController::class, 'clear'])
    ->whereNumber('userId');
