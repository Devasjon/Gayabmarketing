<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('localization.supported'));
        $locale = $request->cookie(config('localization.cookie'));

        if (! in_array($locale, $supported, true)) {
            $locale = $request->getPreferredLanguage($supported) ?? config('app.fallback_locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
