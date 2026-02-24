<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

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

        // Share "logged in as" (impersonation) state for ts_commune views — badge + read-only edit buttons.
        View::composer([
            'users.dashboard',
            'users.tuteurs_list',
            'users.students_list',
            'users.add_student',
            'users.pending_requests',
            'users.approved_requests',
        ], function ($view) {
            $view->with('logged_in_as', session('impersonate_read_only') ? session('logged_in_as_name') : null);
            $view->with('impersonate_read_only', (bool) session('impersonate_read_only'));
        });
    }
}
