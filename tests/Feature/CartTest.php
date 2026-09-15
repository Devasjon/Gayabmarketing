<?php

namespace Tests\Feature;

use App\Livewire\AddToCartButton;
use App\Livewire\CartPage;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_prompt_instead_of_add_to_cart(): void
    {
        $product = Product::factory()->create();

        Livewire::test(AddToCartButton::class, ['product' => $product])
            ->assertSeeHtml(route('login'));
    }

    public function test_authenticated_user_can_add_a_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Livewire::actingAs($user)
            ->test(AddToCartButton::class, ['product' => $product])
            ->call('add');

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
    }

    public function test_adding_the_same_product_twice_increments_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Livewire::actingAs($user)->test(AddToCartButton::class, ['product' => $product])->call('add');
        Livewire::actingAs($user)->test(AddToCartButton::class, ['product' => $product])->call('add');

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_user_can_update_quantity_and_remove_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = $user->cart()->create();
        $item = $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        Livewire::actingAs($user)->test(CartPage::class)
            ->call('updateQuantity', $item->id, 3);

        $this->assertSame(3, CartItem::find($item->id)->quantity);

        Livewire::actingAs($user)->test(CartPage::class)
            ->call('remove', $item->id);

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_user_cannot_modify_another_users_cart_item(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $product = Product::factory()->create();
        $cart = $owner->cart()->create();
        $item = $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        try {
            Livewire::actingAs($intruder)->test(CartPage::class)->call('remove', $item->id);
        } catch (\Throwable) {
            // a 404/authorization failure here is the expected, safe outcome
        }

        $this->assertDatabaseHas('cart_items', ['id' => $item->id]);
    }
}
