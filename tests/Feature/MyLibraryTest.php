<?php

namespace Tests\Feature;

use App\Livewire\MyLibrary;
use App\Models\Entitlement;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_only_shows_the_current_users_entitled_products(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $mine = Product::factory()->create();
        $mine->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'My Product', 'description' => 'd']);

        $notMine = Product::factory()->create();
        $notMine->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Not Mine', 'description' => 'd']);

        $order = Order::create(['reference' => 'GBM-1', 'user_id' => $user->id, 'customer_phone' => '0123456789', 'subtotal_cents' => 100, 'total_cents' => 100, 'currency' => 'MYR', 'status' => 'paid']);
        Entitlement::create(['user_id' => $user->id, 'product_id' => $mine->id, 'order_id' => $order->id, 'granted_at' => now()]);
        Entitlement::create(['user_id' => $other->id, 'product_id' => $notMine->id, 'order_id' => $order->id, 'granted_at' => now()]);

        Livewire::actingAs($user)->test(MyLibrary::class)
            ->assertSee('My Product')
            ->assertDontSee('Not Mine');
    }
}
