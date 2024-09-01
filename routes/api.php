<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
// routes/api.php
use App\Http\Controllers\OrderController;

Route::get('orders/getCategories', [OrderController::class, 'getCategories'])->name('api.orders.getCategories');
Route::get('orders/getServices', [OrderController::class, 'getServices'])->name('api.orders.getServices');
Route::get('orders/search', [OrderController::class, 'search'])->name('api.orders.search');
Route::get('orders/searchServices', [OrderController::class, 'searchServices'])->name('api.orders.searchServices');
