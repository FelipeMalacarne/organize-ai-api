<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Tag;
use App\Policies\DocumentPolicy;
use App\Policies\TagPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Document::class => DocumentPolicy::class,
        Tag::class => TagPolicy::class,
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
