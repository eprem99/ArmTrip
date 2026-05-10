<?php

namespace App\Http\Middleware;

use App\Models\Option;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = ltrim($request->path(), '/');

        // Never block admin/auth/tools routes.
        $excludedPrefixes = [
            'admin',
            'login',
            'register',
            'logout',
            'locale',
            'subscribe',
            'up',
        ];

        foreach ($excludedPrefixes as $p) {
            if ($path === $p || str_starts_with($path, $p.'/')) {
                return $next($request);
            }
        }

        // Allow access to the pages themselves.
        if ($request->routeIs('front.coming_soon') || $request->routeIs('front.maintenance')) {
            return $next($request);
        }

        $maintenance = (string) Option::get('site_maintenance_enabled', '0');
        if ($maintenance === '1') {
            return response()->view('front.maintenance', [], 503);
        }

        $comingSoon = (string) Option::get('site_coming_soon_enabled', '0');
        if ($comingSoon === '1') {
            return redirect()->route('front.coming_soon');
        }

        return $next($request);
    }
}

