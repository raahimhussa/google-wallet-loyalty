<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GoogleWalletService;
use App\Services\FakeGoogleWalletService;
use App\Services\FirebaseService;
use App\Services\FakeFirebaseService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // For testing, we will ALWAYS use the FakeGoogleWalletService.
        // This bypasses any configuration issues.
        $this->app->singleton(GoogleWalletService::class, FakeGoogleWalletService::class);

        // For testing, we will ALWAYS use the FakeFirebaseService.
        $this->app->singleton(FirebaseService::class, FakeFirebaseService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
