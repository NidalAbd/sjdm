<?php

use App\Http\Controllers\BonusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// routes/web.php
Route::get('lang/{lang}', function ($lang) {
    session(['applocale' => $lang]);
    app()->setLocale($lang); // Set locale immediately
    return redirect()->back();
})->name('changeLang');


Route::get('/', [WelcomeController::class, 'index'])->name('home');


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
    Route::post('users/{user}/add-balance', [UserController::class, 'processAddBalance'])->name('users.processAddBalance');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
    Route::get('/bonus/request', [BonusController::class, 'requestBonus'])->name('bonus.request');
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');


    // Role Routes
    Route::resource('roles', RoleController::class);
    // Permission Routes
    Route::resource('permissions', PermissionController::class);

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/getServices', [OrderController::class, 'getServices'])->name('orders.getServices');
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');

    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('services/fetch', [ServiceController::class, 'fetchFromApi'])->name('services.fetch');
    Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('/services/filter', [ServiceController::class, 'filter'])->name('services.filter');
    Route::get('/services/getCategories', [ServiceController::class, 'getCategories'])->name('services.getCategories');
    Route::get('/services/categories', [ServiceController::class, 'getCategories'])->name('services.getCategories');

    Route::resource('transactions', TransactionController::class);


    Route::resource('support', SupportTicketController::class);


    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('checkout.cancel');
    // Routes for admin to add balance manually


});
