<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\OrderController;

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

// Order routes
Route::get('orders/getCategories', [OrderController::class, 'getCategories'])->name('api.orders.getCategories');
Route::get('orders/getServices', [OrderController::class, 'getServices'])->name('api.orders.getServices');
Route::get('orders/search', [OrderController::class, 'search'])->name('api.orders.search');
Route::get('orders/searchServices', [OrderController::class, 'searchServices'])->name('api.orders.searchServices');
Route::get('/orders/{order}/refill', [OrderController::class, 'checkRefill'])->name('orders.checkRefill');
Route::get('/orders/{order}/cancel', [OrderController::class, 'checkCancel'])->name('orders.checkCancel');

// Order Actions - Use numeric constraint to avoid catching other routes
Route::get('/{order}/check-cancel', [\App\Services\Api::class, 'checkCancel'])->where('order', '[0-9]+')->name('checkCancel');
Route::post('/{orderId}/cancel', [\App\Services\Api::class, 'cancel'])->where('orderId', '[0-9]+')->name('orders.cancel');
Route::get('/{order}/check-refill', [\App\Services\Api::class, 'checkRefill'])->where('order', '[0-9]+')->name('checkRefill');
Route::post('/{order}/refill', [\App\Services\Api::class, 'refill'])->where('order', '[0-9]+')->name('orders.refill');


