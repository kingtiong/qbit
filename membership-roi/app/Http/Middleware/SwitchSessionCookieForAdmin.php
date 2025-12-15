<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SwitchSessionCookieForAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Admin area uses its own session cookie so it doesn't overwrite the member session.
        if ($request->is('quantumbitv9') || $request->is('quantumbitv9/*')) {
            $base = Str::slug((string) config('app.name', 'laravel'));
            config([
                'session.cookie' => $base.'-admin-session',
            ]);
        }

        return $next($request);
    }
}

