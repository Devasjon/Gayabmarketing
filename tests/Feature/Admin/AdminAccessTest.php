<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_customers_are_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Customer->value);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admins_can_access_the_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_support_can_reach_admin_but_not_the_product_manager(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Support->value);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('admin.products'))->assertForbidden();
    }
}
