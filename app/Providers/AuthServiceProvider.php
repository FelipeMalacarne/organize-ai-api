<?php

namespace App\Providers;

use App\Services\Auth\RegisterService;
use App\Services\Auth\SanctumRegister;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        App::bind(RegisterService::class, SanctumRegister::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
