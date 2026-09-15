<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole(Role::Customer->value));
    }

    public function test_registration_is_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post('/register', [
                'name' => 'Test User',
                'email' => "test{$i}@example.com",
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            // Each successful attempt logs the new user in, and the 'guest'
            // middleware on /register would otherwise redirect subsequent
            // attempts before they ever reach the throttle check.
            $this->post('/logout');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'onemore@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(429);
    }
}
