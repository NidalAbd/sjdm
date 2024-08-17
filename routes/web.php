<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('home');

Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');

Route::middleware(['auth'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assignRole');
        Route::post('users/{user}/assign-role', [UserController::class, 'storeAssignRole'])->name('users.storeAssignRole');
        Route::get('users/{user}/assign-permission', [UserController::class, 'assignPermission'])->name('users.assignPermission');
        Route::post('users/{user}/assign-permission', [UserController::class, 'storeAssignPermission'])->name('users.storeAssignPermission');
        Route::get('users/{user}/assignTask', [UserController::class, 'assignTask'])->name('users.assignTask');
        Route::post('users/{user}/assignTask', [UserController::class, 'storeAssignTask']);
        Route::post('users/{user}/assign-project', [UserController::class, 'storeAssignProject'])->name('users.store_assign_project');
        Route::get('users/{user}/assignProject', [UserController::class, 'assignProject'])->name('users.assignProject');
        Route::post('users/{user}/assignProject', [UserController::class, 'storeAssignProject']);
        // Role Routes
        Route::resource('roles', RoleController::class);
        // Permission Routes
        Route::resource('permissions', PermissionController::class);

        Route::resource('orders', OrderController::class);
        Route::resource('services', ServiceController::class);
        Route::resource('transactions', TransactionController::class);

    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('checkout.cancel');
    });
