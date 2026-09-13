<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Livewire\Admin\CategoryManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);

        return $user;
    }

    public function test_admin_can_create_a_category(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CategoryManager::class)
            ->set('name', 'Spreadsheet')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', ['name' => 'Spreadsheet', 'slug' => 'spreadsheet']);
    }

    public function test_deleting_a_category_with_products_is_blocked(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($this->admin())
            ->test(CategoryManager::class)
            ->call('delete', $category->id);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_deleting_an_unused_category_succeeds(): void
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->admin())
            ->test(CategoryManager::class)
            ->call('delete', $category->id);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
