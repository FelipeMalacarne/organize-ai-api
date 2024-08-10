<?php

namespace App\Providers;

use App\Services\Auth\LoginService;
use App\Services\Auth\RegisterService;
use App\Services\Auth\SanctumLogin;
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
        App::bind(LoginService::class, SanctumLogin::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
