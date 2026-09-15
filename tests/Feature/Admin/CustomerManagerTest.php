<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Livewire\Admin\CustomerManager;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function support(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Support->value);

        return $user;
    }

    public function test_content_manager_cannot_view_customers(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ContentManager->value);

        $this->actingAs($user)->get(route('admin.customers'))->assertForbidden();
    }

    public function test_support_can_list_customers(): void
    {
        $customer = User::factory()->create(['name' => 'Alice Tan']);
        $customer->assignRole(Role::Customer->value);

        Livewire::actingAs($this->support())
            ->test(CustomerManager::class)
            ->assertSee('Alice Tan');
    }

    public function test_support_can_add_a_note_to_a_customer(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::Customer->value);

        Livewire::actingAs($this->support())
            ->test(CustomerManager::class)
            ->call('view', $customer->id)
            ->set('noteBody', 'Called about a refund question.')
            ->call('addNote')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customer_notes', [
            'user_id' => $customer->id,
            'body' => 'Called about a refund question.',
        ]);
    }
}
