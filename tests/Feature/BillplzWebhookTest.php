<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BillplzWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'test-x-signature-secret';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.billplz.x_signature' => self::SECRET]);
        Mail::fake();
    }

    private function makeOrder(int $amountCents = 5000): Order
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price_cents' => $amountCents]);

        $order = Order::create([
            'reference' => 'GBM-TEST-'.uniqid(),
            'user_id' => $user->id,
            'customer_phone' => '0123456789',
            'subtotal_cents' => $amountCents,
            'total_cents' => $amountCents,
            'currency' => 'MYR',
            'status' => 'pending',
            'billplz_bill_id' => 'bill-'.uniqid(),
        ]);

        $order->items()->create([
            'product_id' => $product->id, 'product_name' => $product->translation('en')?->name ?? 'Product',
            'unit_price_cents' => $amountCents, 'quantity' => 1, 'line_total_cents' => $amountCents,
        ]);

        $order->payment()->create([
            'provider_bill_id' => $order->billplz_bill_id, 'amount_cents' => $amountCents, 'currency' => 'MYR', 'status' => 'pending',
        ]);

        return $order;
    }

    private function callbackPayload(Order $order, bool $paid = true, ?int $amount = null): array
    {
        $payload = [
            'id' => $order->billplz_bill_id,
            'collection_id' => 'col-1',
            'paid' => $paid ? 'true' : 'false',
            'state' => $paid ? 'paid' : 'due',
            'amount' => $amount ?? $order->total_cents,
            'paid_amount' => $amount ?? $order->total_cents,
        ];

        ksort($payload);
        $source = collect($payload)->map(fn ($v, $k) => $k.$v)->implode('|');
        $payload['x_signature'] = hash_hmac('sha256', $source, self::SECRET);

        return $payload;
    }

    public function test_valid_paid_callback_marks_order_paid_and_grants_entitlement(): void
    {
        $order = $this->makeOrder();
        $cart = Cart::create(['user_id' => $order->user_id]);
        $cart->items()->create(['product_id' => $order->items->first()->product_id, 'quantity' => 1]);

        $this->post(route('billplz.callback'), $this->callbackPayload($order))->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'status' => 'paid']);
        $this->assertDatabaseHas('entitlements', ['user_id' => $order->user_id, 'product_id' => $order->items->first()->product_id]);
        $this->assertDatabaseHas('invoices', ['order_id' => $order->id]);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('payment_events', ['order_id' => $order->id, 'signature_valid' => 1]);
        Mail::assertQueued(OrderConfirmationMail::class, fn ($mail) => $mail->order->id === $order->id);
    }

    public function test_invalid_signature_does_not_mark_order_paid(): void
    {
        $order = $this->makeOrder();
        $payload = $this->callbackPayload($order);
        $payload['x_signature'] = 'tampered';

        $this->post(route('billplz.callback'), $payload)->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertDatabaseMissing('entitlements', ['user_id' => $order->user_id]);
        $this->assertDatabaseHas('payment_events', ['order_id' => $order->id, 'signature_valid' => 0]);
    }

    public function test_unpaid_callback_does_not_mark_order_paid(): void
    {
        $order = $this->makeOrder();

        $this->post(route('billplz.callback'), $this->callbackPayload($order, paid: false))->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::Pending, $order->status);
    }

    public function test_duplicate_callback_is_idempotent(): void
    {
        $order = $this->makeOrder();

        $this->post(route('billplz.callback'), $this->callbackPayload($order))->assertOk();
        $this->post(route('billplz.callback'), $this->callbackPayload($order))->assertOk();

        $this->assertDatabaseCount('entitlements', 1);
        $this->assertDatabaseCount('invoices', 1);
        Mail::assertQueued(OrderConfirmationMail::class, 1);
    }

    public function test_amount_mismatch_refuses_to_mark_paid(): void
    {
        $order = $this->makeOrder(amountCents: 5000);

        $this->post(route('billplz.callback'), $this->callbackPayload($order, amount: 100))->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertDatabaseMissing('entitlements', ['user_id' => $order->user_id]);
    }

    public function test_callback_for_unknown_bill_id_is_ignored_safely(): void
    {
        $payload = [
            'id' => 'does-not-exist', 'collection_id' => 'col-1', 'paid' => 'true', 'state' => 'paid', 'amount' => 5000, 'paid_amount' => 5000,
        ];
        ksort($payload);
        $source = collect($payload)->map(fn ($v, $k) => $k.$v)->implode('|');
        $payload['x_signature'] = hash_hmac('sha256', $source, self::SECRET);

        $this->post(route('billplz.callback'), $payload)->assertOk();

        $this->assertDatabaseCount('entitlements', 0);
        $this->assertDatabaseCount('payment_events', 0);
    }
}
