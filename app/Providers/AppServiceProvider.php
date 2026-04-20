<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Domain\Setting\Services\SettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!app()->isLocal()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
