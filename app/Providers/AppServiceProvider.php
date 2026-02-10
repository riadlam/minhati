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
        // Use the request's host for URLs so links stay on same origin and session cookie is sent.
        // (Otherwise APP_URL / MINHATI_APP_URL can make route() point to another host and cookie is not sent.)
        if ($this->app->runningInConsole() === false && request()->hasHeader('Host')) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }
    }
}
