<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GoogleWalletService;
use App\Services\FirebaseService;

class GoogleWalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GoogleWalletService::class, function ($app) {
            return new GoogleWalletService();
        });
        
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });
    }

    public function boot()
    {
        //
    }
}