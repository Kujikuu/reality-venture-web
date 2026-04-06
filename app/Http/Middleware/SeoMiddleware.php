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
        Inertia::share('seo', fn () => [
            'title' => config('app.name'),
            'description' => 'Reality Venture - Accelerator and incubator program connecting startups with expert consultants',
            'canonical' => $request->url(),
            'ogImage' => asset('images/og-default.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',
            'jsonLd' => null,
        ]);

        return $next($request);
    }
}
