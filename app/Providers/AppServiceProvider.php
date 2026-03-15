<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS when APP_URL is https.
        // This ensures @vite(), asset(), and url() all generate https:// links
        // when running behind Railway's reverse proxy.
        if (str_starts_with(config('app.url', 'http://'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
