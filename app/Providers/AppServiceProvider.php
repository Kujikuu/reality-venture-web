<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, fn () => new StripeClient(config('services.stripe.secret')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Limits for the public applicant routes, which have no login.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('applications-submit', fn (Request $request): array => [
            Limit::perMinute(5)->by('minute:'.$request->ip()),
            Limit::perHour(30)->by('hour:'.$request->ip()),
        ]);

        RateLimiter::for('applications-view', fn (Request $request): array => [
            Limit::perMinute(30)->by('ip:'.$request->ip()),
            Limit::perMinute(20)->by('uid:'.$request->route('uid')),
        ]);

        RateLimiter::for('agreement-sign', fn (Request $request): array => [
            Limit::perMinute(5)->by('ip:'.$request->ip()),
            Limit::perMinute(5)->by('uid:'.$request->route('uid')),
        ]);

        RateLimiter::for('hub-export', fn (Request $request): Limit => Limit::perMinute(120)->by($request->ip()));
    }
}
