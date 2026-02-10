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
        // Force URL + session cookie settings from the current request host/scheme.
        // This avoids APP_URL/.env mismatches that cause missing session cookies on navigation.
        if ($this->app->runningInConsole() === false && request()->hasHeader('Host')) {
            config([
                'session.domain' => request()->getHost(),
                'session.secure' => request()->isSecure(),
            ]);
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }
    }
}
