<?php

use App\Http\Controllers\BonusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PointsController;
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
    // Set the language in session and app locale
    session(['applocale' => $lang]);
    app()->setLocale($lang);

    // If the user is authenticated, save the language to the user profile
    if (auth()->check()) {
        $user = auth()->user();
        $user->language = $lang;
        $user->save();
    }

    return redirect()->back();
})->name('changeLang');

// Static content routes
Route::get('/terms-and-conditions', [WelcomeController::class, 'terms'])->name('terms');
Route::get('/faq', [WelcomeController::class, 'faq'])->name('faq');
Route::get('/about', [WelcomeController::class, 'about'])->name('about');
Route::get('/how-it-works', [WelcomeController::class, 'howItWorks'])->name('how-it-works');
Route::get('/support_take', [WelcomeController::class, 'support'])->name('support.take');
Route::get('/privacy-policy', [WelcomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/contact-us', [WelcomeController::class, 'contact'])->name('contact');
Route::get('/all-services', [ServiceController::class, 'getAllServices'])->name('services.all');

Route::get('/sitemap.xml', function () {
    return response()->file(public_path('sitemap.xml'));
})->name('sitemap');

// Auth routes
Auth::routes(['verify' => false]);

// Home and dashboard routes
Route::get('/home', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('/orders/updateStatuses', [OrderController::class, 'updateOrderStatuses'])->name('orders.updateStatuses');
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Protected routes
Route::middleware(['auth', 'check.banned'])->group(function () {

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
    Route::get('/referrals', [UserController::class, 'referalIndex'])->name('referrals.index');
    Route::post('/profile/update-image', [UserController::class, 'updateImage'])->name('profile.update.image');
    Route::post('/users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggleBan');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    // Role and permission routes
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // Order routes
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
//    Route::get('/orders/getServices', [OrderController::class, 'getServices'])->name('orders.getServices');
//    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
//    Route::get('/orders/getCategories', [OrderController::class, 'getCategories'])->name('orders.getCategories');


    // Service routes
    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('services', [ServiceController::class, 'store'])->name('services.store');
// In routes/web.php or routes/api.php

    Route::get('services/fetch-en', [ServiceController::class, 'fetchFromApiEn'])->name('services.fetchEn');
    Route::get('services/fetch-ar', [ServiceController::class, 'fetchFromApiAr'])->name('services.fetchAr');

    Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('/services/filter', [ServiceController::class, 'filter'])->name('services.filter');
    Route::get('/services/getCategories', [ServiceController::class, 'getCategories'])->name('services.getCategories');

    Route::resource('transactions', TransactionController::class);

    // Support Ticket Routes
    Route::prefix('support')->middleware(['auth'])->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('support.index');
        Route::get('/create', [SupportTicketController::class, 'create'])->name('support.create');
        Route::post('/', [SupportTicketController::class, 'store'])->name('support.store');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
        Route::get('/{ticket}/edit', [SupportTicketController::class, 'edit'])->name('support.edit');
        Route::put('/{ticket}', [SupportTicketController::class, 'update'])->name('support.update');
        Route::delete('/{ticket}', [SupportTicketController::class, 'destroy'])->name('support.destroy');
        Route::post('/{ticket}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/{ticket}/close', [SupportTicketController::class, 'closeTicket'])->name('support.close');

    });

    Route::get('notifications/latest', [NotificationController::class, 'fetchLatest'])->name('notifications.latest');
    Route::get('notifications/{id}/markAsRead', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
// web.php
    Route::get('notifications/loadMore', [NotificationController::class, 'loadMore'])->name('notifications.loadMore');
    Route::post('/notifications/markAllAsRead', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markAllAsRead');

    Route::get('/checkout/cancel/{transaction_id}', [StripeController::class, 'cancel'])->name('checkout.cancel');
    Route::get('/checkout/success/{transaction_id}', [StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/fail/{transaction_id}', [StripeController::class, 'fail'])->name('checkout.fail');
    Route::post('/webhook/stripe', [StripeController::class, 'handleWebhook']);
    Route::get('/transactions/complete/{transaction_id}', [StripeController::class, 'completeTransaction'])->name('transactions.complete');

    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
//    Route::get('/checkout/success', [StripeController::class, 'success'])->name('checkout.success');
//    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('checkout.cancel');
    Route::get('/points', [PointsController::class, 'index'])->name('points.index');
    Route::post('/points/redeem', [PointsController::class, 'redeem'])->name('points.redeem');
});
