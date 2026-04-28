<?php

use App\Http\Middleware\EnsureConsultantIsApproved;
use App\Http\Middleware\EnsureUserIsClient;
use App\Http\Middleware\EnsureUserIsConsultant;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SeoMiddleware;
use App\Http\Middleware\SetLocaleFromPreference;
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
        $middleware->web(append: [
            SetLocaleFromPreference::class,
            HandleInertiaRequests::class,
            SeoMiddleware::class,
        ]);

        $middleware->alias([
            'role.client' => EnsureUserIsClient::class,
            'role.consultant' => EnsureUserIsConsultant::class,
            'consultant.approved' => EnsureConsultantIsApproved::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'webhooks/calendly',
        ]);

        $middleware->encryptCookies(except: [
            'rv_locale',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
