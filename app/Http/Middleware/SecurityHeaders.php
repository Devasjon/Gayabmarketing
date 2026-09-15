<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Str::random(32);

        app(Vite::class)->useCspNonce($nonce);
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        // Alpine.js (bundled with Livewire) evaluates directive expressions via
        // the Function constructor, so script-src needs 'unsafe-eval' unless the
        // app opts into Livewire's separate csp_safe build (livewire.csp_safe).
        // Alpine also toggles the `style` attribute directly (x-show, x-cloak,
        // x-transition), which CSP treats as inline style regardless of a nonce
        // on the element — a nonce source in style-src would only cover <style>
        // tags and still block those attribute writes, so style-src relies on
        // 'unsafe-inline' instead. Everything else is nonce/self-scoped to keep
        // injected <script> tags and third-party origins out.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}
