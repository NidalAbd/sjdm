<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        // Fix asset URL for AdminLTE when app is at same level as public_html
        if (config('app.env') === 'production') {
            $this->app->bind('path.public', function() {
                return base_path('public_html');
            });
        }
    }
}