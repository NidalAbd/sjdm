<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes for SPA
Route::get('/services', [HomeController::class, 'services']);
Route::get('/services/{id}', [HomeController::class, 'service']);
Route::get('/stats', [HomeController::class, 'stats']);
Route::get('/categories', [HomeController::class, 'categories']);
Route::get('/platforms', [HomeController::class, 'platforms']);
Route::get('/featured', [HomeController::class, 'featured']);

// routes/api.php
use App\Http\Controllers\OrderController;

Route::get('orders/getCategories', [OrderController::class, 'getCategories'])->name('api.orders.getCategories');
Route::get('orders/getServices', [OrderController::class, 'getServices'])->name('api.orders.getServices');
Route::get('orders/search', [OrderController::class, 'search'])->name('api.orders.search');
Route::get('orders/searchServices', [OrderController::class, 'searchServices'])->name('api.orders.searchServices');
Route::get('/orders/{order}/refill', [OrderController::class, 'checkRefill'])->name('orders.checkRefill');
Route::get('/orders/{order}/cancel', [OrderController::class, 'checkCancel'])->name('orders.checkCancel');

// Order Actions
Route::get('/{order}/check-cancel', [\App\Services\Api::class, 'checkCancel'])->name('checkCancel');
Route::post('/{orderId}/cancel', [\App\Services\Api::class, 'cancel'])->name('orders.cancel');
Route::get('/{order}/check-refill', [\App\Services\Api::class, 'checkRefill'])->name('checkRefill');
Route::post('/{order}/refill', [\App\Services\Api::class, 'refill'])->name('orders.refill');


