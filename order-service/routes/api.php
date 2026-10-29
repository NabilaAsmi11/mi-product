<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/health', [OrderController::class, 'health']);

Route::get('/orders', [OrderController::class, 'index']);

Route::post('/orders', [OrderController::class, 'store']);

Route::get('/orders/user/{userId}', [OrderController::class, 'getByUser'])
    ->whereNumber('userId');

Route::get('/orders/{id}', [OrderController::class, 'show'])
    ->whereNumber('id');

Route::patch('/orders/{id}/pay', [OrderController::class, 'pay'])
    ->whereNumber('id');
