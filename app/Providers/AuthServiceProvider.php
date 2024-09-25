<?php

namespace App\Providers;

use App\Models\Document;
use App\Policies\DocumentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Document::class => DocumentPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // App::bind(RegisterService::class, SanctumRegister::class);
        // App::bind(LoginService::class, SanctumLogin::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
