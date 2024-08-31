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

// Language change route
Route::get('lang/{lang}', function ($lang) {
    session(['applocale' => $lang]);
    app()->setLocale($lang); // Set locale immediately
    return redirect()->back();
})->name('changeLang');

// Public route for the welcome page
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Auth routes with email verification enabled
Auth::routes(['verify' => true]);

// Home route, redirecting to dashboard
Route::get('/home', [HomeController::class, 'index'])->name('dashboard')->middleware('auth', 'verified');

// Middleware group for authenticated and verified users
Route::middleware(['auth', 'verified'])->group(function () {
    // User routes
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

    // Profile settings routes
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    // Bonus request route
    Route::get('/bonus/request', [BonusController::class, 'requestBonus'])->name('bonus.request');

    // Role and Permission routes
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // Order routes
    Route::resource('orders', OrderController::class)->except(['edit', 'update']);
    Route::get('/orders/getServices', [OrderController::class, 'getServices'])->name('orders.getServices');
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');

    // Service routes
    Route::resource('services', ServiceController::class);
    Route::get('services/fetch', [ServiceController::class, 'fetchFromApi'])->name('services.fetch');
    Route::get('/services/filter', [ServiceController::class, 'filter'])->name('services.filter');
    Route::get('/services/getCategories', [ServiceController::class, 'getCategories'])->name('services.getCategories');

    // Transaction routes
    Route::resource('transactions', TransactionController::class);

    // Support ticket routes
    Route::resource('support', SupportTicketController::class);

    // Stripe checkout routes
    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('checkout.cancel');
});

// Handle email verification notice
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

