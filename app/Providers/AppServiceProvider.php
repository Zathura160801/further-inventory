<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if ($forwardedHost = request()->header('x-forwarded-host')) {
            $scheme = request()->header('x-forwarded-proto') ?: request()->getScheme();
            URL::forceRootUrl($scheme . '://' . $forwardedHost);
            URL::forceScheme($scheme);
        }
    }
}
