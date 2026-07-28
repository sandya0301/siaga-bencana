<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // forceScheme('https') is a belt-and-suspenders fallback for CLI contexts
        // (Artisan, queue workers) where X-Forwarded-Proto headers are absent.
        // We key off APP_URL starting with https:// instead of APP_ENV=production
        // so this works regardless of what environment name Railway uses.
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}