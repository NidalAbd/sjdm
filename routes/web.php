<?php

use App\Http\Controllers\BonusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
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
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


// routes/web.php
Route::get('lang/{lang}', function ($lang) {
    session(['applocale' => $lang]);
    app()->setLocale($lang); // Set locale immediately
    return redirect()->back();
})->name('changeLang');




Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('/orders/updateStatuses', [OrderController::class, 'updateOrderStatuses'])->name('orders.updateStatuses');
Route::get('/', [WelcomeController::class, 'index'])->name('home');

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


    // Role Routes
    Route::resource('roles', RoleController::class);
    // Permission Routes
    Route::resource('permissions', PermissionController::class);

//    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
//    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
//    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
//    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
//    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
//    Route::get('orders/search', [OrderController::class, 'search'])->name('orders.search');
//    Route::get('orders/searchServices', [OrderController::class, 'searchServices'])->name('orders.searchServices');
//    Route::get('orders/getCategories', [OrderController::class, 'getCategories'])->name('orders.getCategories');
//    Route::get('orders/getServices', [OrderController::class, 'getServices'])->name('orders.getServices');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/getServices', [OrderController::class, 'getServices'])->name('orders.getServices');
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
    Route::get('/orders/getCategories', [OrderController::class, 'getCategories'])->name('orders.getCategories');

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
    });

    Route::get('notifications/latest', [NotificationController::class, 'fetchLatest'])->name('notifications.latest');
    Route::get('notifications/{id}/markAsRead', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('checkout.cancel');
    // Routes for admin to add balance manually


});
// Quick Links
Route::view('/api', 'api')->name('api');
Route::view('/how-it-works', 'how_it_works')->name('how.it.works');
Route::view('/faq', 'faq')->name('faq');
Route::view('/terms-and-conditions', 'terms_and_conditions')->name('terms.and.conditions');
Route::view('/blog', 'blog')->name('blog');

// Company Pages
Route::view('/about-us', 'layouts.footer.about_us')->name('about.us');
Route::view('/careers', 'layouts.footer.careers')->name('careers');
Route::view('/privacy-policy', 'layouts.footer.privacy_policy')->name('privacy.policy');
Route::view('/contact-us', 'layouts.footer.contact_us')->name('contact.us');

// Resources Pages
Route::view('/documentation', 'documentation')->name('documentation');
Route::view('/developer-api', 'developer_api')->name('developer.api');
Route::view('/affiliate-program', 'affiliate_program')->name('affiliate.program');
Route::view('/support', 'support')->name('support');
