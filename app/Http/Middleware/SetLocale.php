<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['en', 'ru', 'am']);
        $locale = session('locale');

        if (! $locale && $request->hasCookie('locale')) {
            $locale = $request->cookie('locale');
            if (in_array($locale, $supported, true)) {
                session(['locale' => $locale]);
            } else {
                $locale = config('app.locale');
            }
        }

        if (! $locale) {
            $locale = config('app.locale');
        }

        if (in_array($locale, $supported, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
