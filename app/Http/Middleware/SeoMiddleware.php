<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SeoMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $noIndexPrefixes = ['login', 'register', 'forgot-password', 'reset-password', 'dashboard', 'consultant/', 'bookings/'];
        $path = $request->path();
        $robots = 'index, follow';

        foreach ($noIndexPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $robots = 'noindex, nofollow';
                break;
            }
        }

        Inertia::share('seo', fn () => [
            'title' => config('app.name'),
            'description' => 'Reality Venture - Accelerator and incubator program connecting startups with expert consultants',
            'canonical' => $request->url(),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => $robots,
            'jsonLd' => null,
        ]);

        return $next($request);
    }
}
