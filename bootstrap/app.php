<?php

use Dotenv\Dotenv;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$settingsDir = $_ENV['APP_SETTINGS_DIR'] ?? '/config';

if (file_exists($settingsDir . '/.env')) {
    $dotenv = Dotenv::createImmutable($settingsDir);
    $dotenv->safeLoad(); // Use safeLoad to prevent exceptions if .env is missing
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
