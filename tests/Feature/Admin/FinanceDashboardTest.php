<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Livewire\Admin\FinanceDashboard;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_content_manager_cannot_view_finance(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ContentManager->value);

        $this->actingAs($user)->get(route('admin.finance'))->assertForbidden();
    }

    public function test_finance_role_sees_total_revenue_from_paid_orders_only(): void
    {
        $customer = User::factory()->create();
        Order::create(['reference' => 'GBM-1', 'user_id' => $customer->id, 'customer_phone' => '0123456789', 'subtotal_cents' => 5000, 'total_cents' => 5000, 'currency' => 'MYR', 'status' => 'paid', 'paid_at' => now()]);
        Order::create(['reference' => 'GBM-2', 'user_id' => $customer->id, 'customer_phone' => '0123456789', 'subtotal_cents' => 3000, 'total_cents' => 3000, 'currency' => 'MYR', 'status' => 'pending']);

        $finance = User::factory()->create();
        $finance->assignRole(Role::Finance->value);

        // Only the paid RM50.00 order should count — the pending RM30.00 one must not.
        Livewire::actingAs($finance)
            ->test(FinanceDashboard::class)
            ->assertSee('RM50.00')
            ->assertDontSee('RM80.00');
    }

    public function test_finance_export_route_requires_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Customer->value);

        $this->actingAs($user)->get(route('admin.finance.export'))->assertForbidden();
    }

    public function test_finance_export_returns_a_csv_for_authorized_staff(): void
    {
        $customer = User::factory()->create();
        Order::create(['reference' => 'GBM-1', 'user_id' => $customer->id, 'customer_phone' => '0123456789', 'subtotal_cents' => 5000, 'total_cents' => 5000, 'currency' => 'MYR', 'status' => 'paid', 'paid_at' => now()]);

        $finance = User::factory()->create();
        $finance->assignRole(Role::Finance->value);

        $response = $this->actingAs($finance)->get(route('admin.finance.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
