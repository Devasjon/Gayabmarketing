<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\Entitlement;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentProcessor
{
    /**
     * Record a Billplz payment notification and, only when it is genuinely
     * valid and paid, mark the order paid and grant entitlements. Safe to
     * call repeatedly for the same bill (duplicate/delayed/out-of-order
     * webhooks and the browser redirect both funnel through here).
     */
    public function handle(
        ?Order $order,
        ?string $billId,
        bool $paid,
        bool $signatureValid,
        ?int $reportedAmountCents,
        array $payload,
        string $type,
    ): void {
        if ($order) {
            PaymentEvent::create([
                'order_id' => $order->id,
                'type' => $type,
                'signature_valid' => $signatureValid,
                'payload' => $this->redact($payload),
                'created_at' => now(),
            ]);
        }

        if (! $order || ! $billId || ! $signatureValid || ! $paid) {
            return;
        }

        DB::transaction(function () use ($order, $billId, $reportedAmountCents) {
            /** @var Order $order */
            $order = Order::whereKey($order->id)->lockForUpdate()->first();
            $payment = Payment::where('order_id', $order->id)->lockForUpdate()->first();

            if (! $payment || $payment->provider_bill_id !== $billId) {
                Log::warning('Billplz payment notification for unknown/mismatched bill', ['order_id' => $order->id, 'bill_id' => $billId]);

                return;
            }

            if ($payment->status === PaymentStatus::Paid) {
                return; // already processed — duplicate/out-of-order notification
            }

            if ($reportedAmountCents !== null && $reportedAmountCents !== $payment->amount_cents) {
                Log::warning('Billplz amount mismatch, refusing to mark paid', [
                    'order_id' => $order->id, 'expected' => $payment->amount_cents, 'reported' => $reportedAmountCents,
                ]);

                return;
            }

            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            $order->update(['status' => 'paid', 'paid_at' => now()]);

            foreach ($order->items()->with('product')->get() as $item) {
                Entitlement::firstOrCreate(
                    ['user_id' => $order->user_id, 'product_id' => $item->product_id],
                    ['order_id' => $order->id, 'granted_at' => now()]
                );
            }

            Invoice::firstOrCreate(
                ['order_id' => $order->id],
                ['invoice_number' => sprintf('INV-%s-%06d', now()->format('Y'), $order->id), 'issued_at' => now()]
            );

            Cart::where('user_id', $order->user_id)->first()?->items()->delete();
        });
    }

    private function redact(array $payload): array
    {
        unset($payload['x_signature']);

        if (isset($payload['billplz']['x_signature'])) {
            unset($payload['billplz']['x_signature']);
        }

        return $payload;
    }
}
