<?php

namespace App\Providers;

use App\Contracts\AuthService;
use App\Contracts\DocumentService;
use App\Contracts\PubSubPublisher;
use App\Services\DocumentService as DocumentServiceImpl;
use App\Services\Pubsub\Publisher;
use App\Services\SocialAuthService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        App::bind(AuthService::class, SocialAuthService::class);
        App::bind(DocumentService::class, DocumentServiceImpl::class);
        App::bind(PubSubPublisher::class, Publisher::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
