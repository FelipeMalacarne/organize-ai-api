<?php

namespace App\Providers;

use App\Services\DocumentAI\IDProcessor;
use App\Services\DocumentAI\TagProcessor;
use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Illuminate\Support\ServiceProvider;

class DocumentAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DocumentProcessorServiceClient::class, function () {
            return new DocumentProcessorServiceClient;
        });

        $this->app->bind(TagProcessor::class, function ($app) {
            return new TagProcessor($app->make(DocumentProcessorServiceClient::class));
        });

        $this->app->bind(IDProcessor::class, function ($app) {
            return new IDProcessor($app->make(DocumentProcessorServiceClient::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
