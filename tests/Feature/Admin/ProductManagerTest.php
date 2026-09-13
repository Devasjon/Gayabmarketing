<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Livewire\Admin\ProductManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductManagerTest extends TestCase
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

    public function test_admin_can_create_a_product(): void
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->admin())
            ->test(ProductManager::class)
            ->call('create')
            ->set('nameEn', 'New Ebook')
            ->set('nameMs', 'Ebook Baharu')
            ->set('descriptionEn', 'A great ebook.')
            ->set('descriptionMs', 'Ebook yang hebat.')
            ->set('categoryId', $category->id)
            ->set('price', '19.90')
            ->set('status', 'published')
            ->set('slug', 'new-ebook')
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::where('slug', 'new-ebook')->firstOrFail();
        $this->assertSame(1990, $product->price_cents);
        $this->assertSame('New Ebook', $product->translation('en')->name);
        $this->assertSame('Ebook Baharu', $product->translation('ms')->name);
    }

    public function test_customer_cannot_open_the_product_manager(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Customer->value);

        $this->actingAs($user)->get(route('admin.products'))->assertForbidden();
    }

    public function test_admin_can_delete_a_product(): void
    {
        $product = Product::factory()->create();

        Livewire::actingAs($this->admin())
            ->test(ProductManager::class)
            ->call('delete', $product->id);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
