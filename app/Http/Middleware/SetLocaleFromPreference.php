<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromPreference
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('rv_locale')
            ?? $request->query('locale')
            ?? $request->getPreferredLanguage(['en', 'ar']);

        if (in_array($locale, ['en', 'ar'], true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
