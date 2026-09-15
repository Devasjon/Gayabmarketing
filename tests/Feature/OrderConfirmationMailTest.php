<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderConfirmationMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_the_order_reference_items_and_subject(): void
    {
        Mail::fake();

        $user = User::factory()->create(['name' => 'Alice Tan']);
        $order = Order::create([
            'reference' => 'GBM-260915-ABC123', 'user_id' => $user->id, 'customer_phone' => '0123456789',
            'subtotal_cents' => 3900, 'total_cents' => 3900, 'currency' => 'MYR', 'status' => 'paid', 'paid_at' => now(),
        ]);
        $order->items()->create(['product_id' => Product::factory()->create()->id, 'product_name' => 'Homestay Guide', 'unit_price_cents' => 3900, 'quantity' => 1, 'line_total_cents' => 3900]);

        Mail::to($user->email)->send(new OrderConfirmationMail($order->load(['items', 'user'])));

        // OrderConfirmationMail implements ShouldQueue, so Mail::send() routes it
        // through the queue rather than sending synchronously — assert queued.
        Mail::assertQueued(OrderConfirmationMail::class, function (OrderConfirmationMail $mail) use ($user) {
            $mail->assertSeeInHtml('GBM-260915-ABC123');
            $mail->assertSeeInHtml('Homestay Guide');
            $mail->assertSeeInHtml('Alice Tan');

            return $mail->hasTo($user->email) && str_contains($mail->subject, 'GBM-260915-ABC123');
        });
    }
}
