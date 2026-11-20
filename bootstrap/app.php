<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'toko.buka' => \App\Http\Middleware\TokoBuka::class,
        ]);

        // Di belakang tunnel / proxy HTTPS (Cloudflare, ngrok, hosting) Laravel harus percaya header X-Forwarded-Proto.
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'midtrans/notifikasi',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
