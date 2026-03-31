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

// Locale setting for SPA - supports all 17 languages
Route::post('set-locale', function (Request $request) {
    $locale = $request->input('locale', 'en');
    $supported = array_keys(config('app.available_locales', ['en' => 'English']));
    if (in_array($locale, $supported)) {
        session(['applocale' => $locale]);
        app()->setLocale($locale);

        if (auth()->check()) {
            $user = auth()->user();
            $user->language = $locale;
            $user->save();
        }

        return response()->json(['success' => true, 'locale' => $locale]);
    }
    return response()->json(['success' => false, 'message' => 'Invalid locale'], 400);
})->middleware('web');

// Languages & Translations API (dynamic, database-driven)
Route::get('/languages', [App\Http\Controllers\Api\LanguageApiController::class, 'index']);
Route::get('/translations/{locale}', [App\Http\Controllers\Api\LanguageApiController::class, 'translations']);
Route::get('/translations/{locale}/check', [App\Http\Controllers\Api\LanguageApiController::class, 'checkUpdates']);

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

// =============================================
// AUTHENTICATED API ROUTES (For Vue Admin Dashboard)
// =============================================

Route::middleware(['web', 'auth'])->group(function () {
    // User/Auth endpoints
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->load('roles', 'permissions', 'media');

        // Get avatar URL from media
        $avatar = null;
        $media = $user->media()->first();
        if ($media) {
            $avatar = \Illuminate\Support\Facades\Storage::url($media->path);
        }

        return response()->json([
            'user' => array_merge($user->toArray(), ['avatar' => $avatar]),
            'is_admin' => $user->hasRole('admin'),
            'roles' => $user->roles->pluck('name')->toArray(),
            'permissions' => $user->permissions->pluck('name')->toArray(),
        ]);
    });

    // Dashboard Stats
    Route::get('/dashboard', [App\Http\Controllers\Api\DashboardController::class, 'stats']);
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats']);
    Route::get('/dashboard/admin-stats', [App\Http\Controllers\Api\DashboardController::class, 'adminStats']);

    // Orders API
    Route::get('/orders', [App\Http\Controllers\Api\OrderApiController::class, 'index']);
    Route::post('/orders', [App\Http\Controllers\Api\OrderApiController::class, 'store']);
    Route::get('/orders/{order}', [App\Http\Controllers\Api\OrderApiController::class, 'show']);
    Route::post('/orders/{order}/refund', [App\Http\Controllers\Api\OrderApiController::class, 'refund']);
    Route::post('/orders/sync', [App\Http\Controllers\Api\OrderApiController::class, 'sync']);

    // Transactions API
    Route::get('/transactions', [App\Http\Controllers\Api\TransactionApiController::class, 'index']);
    Route::post('/transactions/checkout', [App\Http\Controllers\Api\TransactionApiController::class, 'checkout']);

    // Support Tickets API
    Route::get('/support', [App\Http\Controllers\Api\SupportApiController::class, 'index']);
    Route::post('/support', [App\Http\Controllers\Api\SupportApiController::class, 'store']);
    Route::get('/support/{ticket}', [App\Http\Controllers\Api\SupportApiController::class, 'show']);
    Route::post('/support/{ticket}/messages', [App\Http\Controllers\Api\SupportApiController::class, 'addMessage']);
    Route::post('/support/{ticket}/close', [App\Http\Controllers\Api\SupportApiController::class, 'close']);
    Route::post('/support/{ticket}/mark-read', [App\Http\Controllers\Api\SupportApiController::class, 'markRead']);

    // Notifications API
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationApiController::class, 'index']);
    Route::get('/notifications/unread-count', [App\Http\Controllers\Api\NotificationApiController::class, 'unreadCount']);
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Api\NotificationApiController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [App\Http\Controllers\Api\NotificationApiController::class, 'destroy']);

    // Profile API
    Route::put('/profile', [App\Http\Controllers\Api\ProfileApiController::class, 'update']);
    Route::put('/profile/password', [App\Http\Controllers\Api\ProfileApiController::class, 'updatePassword']);
    Route::post('/profile/avatar', [App\Http\Controllers\Api\ProfileApiController::class, 'updateAvatar']);

    // Referrals API
    Route::get('/referrals', [App\Http\Controllers\Api\ReferralApiController::class, 'index']);

    // Points API
    Route::get('/points', [App\Http\Controllers\Api\PointsApiController::class, 'index']);
    Route::post('/points/redeem', [App\Http\Controllers\Api\PointsApiController::class, 'redeem']);

    // Payment Methods (public - for checkout)
    Route::get('/payment-methods', [App\Http\Controllers\Api\PaymentMethodApiController::class, 'index']);

    // =============================================
    // ADMIN ONLY API ROUTES
    // =============================================
    Route::middleware(['can:assign_role'])->prefix('admin')->group(function () {
        // Users Management
        Route::get('/users', [App\Http\Controllers\Api\Admin\UserApiController::class, 'index']);
        Route::post('/users', [App\Http\Controllers\Api\Admin\UserApiController::class, 'store']);
        Route::get('/users/{user}', [App\Http\Controllers\Api\Admin\UserApiController::class, 'show']);
        Route::put('/users/{user}', [App\Http\Controllers\Api\Admin\UserApiController::class, 'update']);
        Route::delete('/users/{user}', [App\Http\Controllers\Api\Admin\UserApiController::class, 'destroy']);
        Route::post('/users/{user}/toggle-ban', [App\Http\Controllers\Api\Admin\UserApiController::class, 'toggleBan']);
        Route::post('/users/{user}/add-balance', [App\Http\Controllers\Api\Admin\UserApiController::class, 'addBalance']);

        // Orders Management (Admin)
        Route::get('/orders', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'index']);
        Route::get('/orders/{order}', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'show']);
        Route::put('/orders/{order}', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'update']);
        Route::delete('/orders/{order}', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'destroy']);
        Route::post('/orders/{order}/refund', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'refund']);
        Route::post('/orders/{order}/cancel', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'cancel']);
        Route::post('/orders/{order}/sync-status', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'syncStatus']);
        Route::post('/orders/{order}/resend', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'resend']);
        Route::post('/orders/sync-all', [App\Http\Controllers\Api\Admin\OrderApiController::class, 'syncAll']);

        // Transactions Management (Admin)
        Route::get('/transactions', [App\Http\Controllers\Api\Admin\TransactionApiController::class, 'index']);
        Route::post('/transactions', [App\Http\Controllers\Api\Admin\TransactionApiController::class, 'store']);
        Route::get('/transactions/{transaction}', [App\Http\Controllers\Api\Admin\TransactionApiController::class, 'show']);
        Route::put('/transactions/{transaction}', [App\Http\Controllers\Api\Admin\TransactionApiController::class, 'update']);
        Route::delete('/transactions/{transaction}', [App\Http\Controllers\Api\Admin\TransactionApiController::class, 'destroy']);
        Route::post('/transactions/{transaction}/approve', [App\Http\Controllers\Api\Admin\TransactionApiController::class, 'approve']);
        Route::post('/transactions/{transaction}/reject', [App\Http\Controllers\Api\Admin\TransactionApiController::class, 'reject']);

        // Support Tickets Management (Admin)
        Route::get('/tickets', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'index']);
        Route::get('/tickets/{ticket}', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'show']);
        Route::put('/tickets/{ticket}', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'update']);
        Route::delete('/tickets/{ticket}', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'destroy']);
        Route::post('/tickets/{ticket}/reply', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'reply']);
        Route::post('/tickets/{ticket}/close', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'close']);
        Route::post('/tickets/{ticket}/reopen', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'reopen']);
        Route::post('/tickets/mark-all-read', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'markAllRead']);
        Route::post('/tickets/bulk-close', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'bulkClose']);
        Route::post('/tickets/bulk-delete', [App\Http\Controllers\Api\Admin\SupportTicketApiController::class, 'bulkDelete']);

        // Services Management (Admin - Full CRUD)
        Route::get('/services', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'index']);
        Route::post('/services', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'store']);
        Route::get('/services/{service}', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'show']);
        Route::put('/services/{service}', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'update']);
        Route::delete('/services/{service}', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'destroy']);
        Route::patch('/services/{service}/toggle', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'toggle']);
        Route::put('/services/{service}/rate', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'updateRate']);
        Route::post('/services/{service}/sync', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'syncService']);
        Route::post('/services/{service}/duplicate', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'duplicate']);
        Route::post('/services/update-all-rates', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'updateAllRates']);
        Route::post('/services/fetch', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'fetchFromApi']);
        Route::post('/services/bulk-toggle', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'bulkToggle']);
        Route::post('/services/bulk-delete', [App\Http\Controllers\Api\Admin\ServiceApiController::class, 'bulkDelete']);

        // Roles Management
        Route::get('/roles', [App\Http\Controllers\Api\Admin\RoleApiController::class, 'index']);
        Route::post('/roles', [App\Http\Controllers\Api\Admin\RoleApiController::class, 'store']);
        Route::put('/roles/{role}', [App\Http\Controllers\Api\Admin\RoleApiController::class, 'update']);
        Route::delete('/roles/{role}', [App\Http\Controllers\Api\Admin\RoleApiController::class, 'destroy']);

        // Permissions Management
        Route::get('/permissions', [App\Http\Controllers\Api\Admin\PermissionApiController::class, 'index']);
        Route::post('/permissions', [App\Http\Controllers\Api\Admin\PermissionApiController::class, 'store']);
        Route::put('/permissions/{permission}', [App\Http\Controllers\Api\Admin\PermissionApiController::class, 'update']);
        Route::delete('/permissions/{permission}', [App\Http\Controllers\Api\Admin\PermissionApiController::class, 'destroy']);

        // Payment Methods Management
        Route::get('/payment-methods', [App\Http\Controllers\Api\Admin\PaymentMethodApiController::class, 'index']);
        Route::post('/payment-methods', [App\Http\Controllers\Api\Admin\PaymentMethodApiController::class, 'store']);
        Route::put('/payment-methods/{paymentMethod}', [App\Http\Controllers\Api\Admin\PaymentMethodApiController::class, 'update']);
        Route::delete('/payment-methods/{paymentMethod}', [App\Http\Controllers\Api\Admin\PaymentMethodApiController::class, 'destroy']);
        Route::patch('/payment-methods/{paymentMethod}/toggle', [App\Http\Controllers\Api\Admin\PaymentMethodApiController::class, 'toggle']);
        Route::post('/payment-methods/{paymentMethod}/move-up', [App\Http\Controllers\Api\Admin\PaymentMethodApiController::class, 'moveUp']);
        Route::post('/payment-methods/{paymentMethod}/move-down', [App\Http\Controllers\Api\Admin\PaymentMethodApiController::class, 'moveDown']);
    });

    // Services API (authenticated)
    Route::get('/services/{service}', [App\Http\Controllers\Api\ServiceApiController::class, 'show']);
    Route::put('/services/{service}', [App\Http\Controllers\Api\ServiceApiController::class, 'update']);
    Route::delete('/services/{service}', [App\Http\Controllers\Api\ServiceApiController::class, 'destroy']);
    Route::post('/services/{service}/sync', [App\Http\Controllers\Api\ServiceApiController::class, 'sync']);
    Route::post('/services/{service}/duplicate', [App\Http\Controllers\Api\ServiceApiController::class, 'duplicate']);

    // Roles & Permissions (for forms)
    Route::get('/roles', [App\Http\Controllers\Api\RoleApiController::class, 'index']);
    Route::get('/permissions', [App\Http\Controllers\Api\PermissionApiController::class, 'index']);

    // =============================================
    // SEO DASHBOARD API ROUTES (Admin)
    // =============================================
    Route::prefix('seo')->group(function () {
        // Dashboard Overview
        Route::get('/dashboard', [App\Http\Controllers\Api\SeoController::class, 'dashboard']);

        // Keywords Management
        Route::get('/keywords', [App\Http\Controllers\Api\SeoController::class, 'keywords']);
        Route::get('/keywords/export', [App\Http\Controllers\Api\SeoController::class, 'exportKeywords']);
        Route::get('/keywords/opportunities', [App\Http\Controllers\Api\SeoController::class, 'keywordOpportunities']);

        // Technical SEO
        Route::get('/issues', [App\Http\Controllers\Api\SeoController::class, 'technicalIssues']);
        Route::post('/issues/{id}/resolve', [App\Http\Controllers\Api\SeoController::class, 'resolveIssue']);

        // Pages
        Route::get('/pages', [App\Http\Controllers\Api\SeoController::class, 'pages']);

        // Structured Data
        Route::get('/structured-data', [App\Http\Controllers\Api\SeoController::class, 'structuredData']);

        // Content Ideas
        Route::get('/content-ideas', [App\Http\Controllers\Api\SeoController::class, 'contentIdeas']);
        Route::post('/content-ideas/{id}/approve', [App\Http\Controllers\Api\SeoController::class, 'approveContentIdea']);

        // Reports
        Route::get('/reports', [App\Http\Controllers\Api\SeoController::class, 'reports']);
        Route::get('/reports/{id}', [App\Http\Controllers\Api\SeoController::class, 'getReport']);
        Route::post('/reports/generate', [App\Http\Controllers\Api\SeoController::class, 'generateReport']);

        // Action Logs
        Route::get('/action-logs', [App\Http\Controllers\Api\SeoController::class, 'actionLogs']);

        // Performance Metrics
        Route::get('/performance/country', [App\Http\Controllers\Api\SeoController::class, 'performanceByCountry']);
        Route::get('/performance/device', [App\Http\Controllers\Api\SeoController::class, 'performanceByDevice']);
        Route::get('/performance/page', [App\Http\Controllers\Api\SeoController::class, 'performanceByPage']);

        // Trends
        Route::get('/trends', [App\Http\Controllers\Api\SeoController::class, 'trends']);
        Route::get('/trends/rising', [App\Http\Controllers\Api\SeoController::class, 'risingKeywords']);

        // SERP Analysis
        Route::get('/serp-analysis', [App\Http\Controllers\Api\SeoController::class, 'serpAnalysis']);
        Route::get('/competitor-analysis', [App\Http\Controllers\Api\SeoController::class, 'competitorAnalysis']);

        // Automation Actions
        Route::post('/sync/all', [App\Http\Controllers\Api\SeoController::class, 'syncAll']);
        Route::post('/sync/gsc', [App\Http\Controllers\Api\SeoController::class, 'syncGsc']);
        Route::post('/sync/trends', [App\Http\Controllers\Api\SeoController::class, 'syncTrends']);
        Route::post('/audit/technical', [App\Http\Controllers\Api\SeoController::class, 'runTechnicalAudit']);
        Route::post('/generate/content-ideas', [App\Http\Controllers\Api\SeoController::class, 'generateContentIdeas']);
        Route::post('/sitemap/update', [App\Http\Controllers\Api\SeoController::class, 'updateSitemap']);
        Route::post('/reindex', [App\Http\Controllers\Api\SeoController::class, 'submitForReindex']);
    });
});


