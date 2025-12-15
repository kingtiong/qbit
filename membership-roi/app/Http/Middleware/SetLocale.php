<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'zh_CN'];

    public function handle(Request $request, Closure $next): Response
    {
        $query = $request->query('lang');
        $cookie = $request->cookie('lang');

        $locale = $this->normalizeLocale(is_string($query) ? $query : null)
            ?? $this->normalizeLocale(is_string($cookie) ? $cookie : null)
            ?? config('app.locale', 'en');

        app()->setLocale($locale);

        // Remember user choice when explicitly set via ?lang=
        if (is_string($query)) {
            $normalized = $this->normalizeLocale($query);
            if ($normalized) {
                cookie()->queue(cookie('lang', $normalized, 60 * 24 * 365)); // 1 year
            }
        }

        return $next($request);
    }

    private function normalizeLocale(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $raw = str_replace('-', '_', trim($raw));

        return match (strtolower($raw)) {
            'en' => 'en',
            'zh', 'cn', 'zh_cn', 'zh_hans' => 'zh_CN',
            default => in_array($raw, self::SUPPORTED, true) ? $raw : null,
        };
    }
}

