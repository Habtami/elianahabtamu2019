<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use App\Listeners\LogAuthenticationActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // The permission matrix is shared for the whole request
        $this->app->singleton(\App\Services\PermissionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        Event::listen(
            Login::class,
            [LogAuthenticationActivity::class, 'handle']
        );

        Event::listen(
            Logout::class,
            [LogAuthenticationActivity::class, 'handle']
        );
    }
}
