<?php

namespace Tests\Feature;

use App\Livewire\Checkout;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function fakeBillplz(): void
    {
        Http::fake([
            '*billplz*/v3/bills' => Http::response(['id' => 'bill-123', 'url' => 'https://billplz-sandbox.test/bills/bill-123'], 200),
        ]);
    }

    public function test_checkout_is_disabled_by_default(): void
    {
        config(['services.billplz.checkout_enabled' => false]);
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = $user->cart()->create();
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        try {
            Livewire::actingAs($user)->test(Checkout::class)
                ->set('phone', '0123456789')
                ->call('pay');
        } catch (\Throwable) {
            // expected: checkout disabled should refuse the request
        }

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_paying_creates_an_order_with_items_and_a_pending_payment(): void
    {
        $this->fakeBillplz();
        config(['services.billplz.checkout_enabled' => true]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['price_cents' => 5000]);
        $cart = $user->cart()->create();
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        Livewire::actingAs($user)->test(Checkout::class)
            ->set('phone', '0123456789')
            ->call('pay');

        $order = Order::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(10000, $order->total_cents);
        $this->assertSame('pending', $order->status->value);
        $this->assertSame('bill-123', $order->billplz_bill_id);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'provider_bill_id' => 'bill-123', 'amount_cents' => 10000]);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'line_total_cents' => 10000]);
    }
}
