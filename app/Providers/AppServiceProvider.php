<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Set default string length for older MySQL versions
        Schema::defaultStringLength(191);

        // Use Bootstrap for pagination views
        Paginator::useBootstrap();

        // Set the application locale based on session or fallback to config default
        // Check if the session has a 'locale' value, otherwise use the default from config
        $locale = Session::get('applocale', config('app.locale'));
        App::setLocale($locale);
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('unreadNotifications', Auth::user()->unreadNotifications);
            }
        });

        View::composer('adminlte::partials.sidebar.menu-item', function ($view) {
            // Check if the menu item is for balance
            if ($view->item['text'] === 'balance' && Auth::check()) {
                $user = Auth::user();
                $view->item['label'] = number_format($user->balance, 2);
                $view->item['label_color'] = $user->balance > 0 ? 'success' : ($user->balance < 0 ? 'danger' : 'white');
            }
        });
    }
}
