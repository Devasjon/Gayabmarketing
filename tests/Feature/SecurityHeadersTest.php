<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_csp_scopes_scripts_and_styles_without_breaking_alpine(): void
    {
        $csp = $this->get(route('home'))->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("'unsafe-eval'", $csp);
        $this->assertMatchesRegularExpression("/script-src[^;]*'nonce-[A-Za-z0-9]+'/", $csp);
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
    }

    public function test_csp_nonce_is_unique_per_request(): void
    {
        $first = $this->get(route('home'))->headers->get('Content-Security-Policy');
        $second = $this->get(route('home'))->headers->get('Content-Security-Policy');

        $this->assertNotSame($first, $second);
    }
}
