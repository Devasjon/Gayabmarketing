<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Order;
use App\Services\BillplzService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Checkout extends Component
{
    #[Validate('required|string|max:30')]
    public string $phone = '';

    public function pay(BillplzService $billplz): mixed
    {
        abort_unless(config('services.billplz.checkout_enabled'), 503, 'Checkout is not open yet.');

        $this->validate();

        $cart = Cart::with('items.product')->where('user_id', auth()->id())->first();

        abort_if(! $cart || $cart->items->isEmpty(), 422, __('cart.checkout.empty_redirect'));

        $order = DB::transaction(function () use ($cart) {
            $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->product->price_cents);

            $order = Order::create([
                'reference' => 'GBM-'.now()->format('ymd').'-'.strtoupper(Str::random(6)),
                'user_id' => auth()->id(),
                'customer_phone' => $this->phone,
                'subtotal_cents' => $subtotal,
                'total_cents' => $subtotal,
                'currency' => 'MYR',
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->localizedName() ?: $item->product->translation('en')?->name,
                    'unit_price_cents' => $item->product->price_cents,
                    'quantity' => $item->quantity,
                    'line_total_cents' => $item->quantity * $item->product->price_cents,
                ]);
            }

            return $order;
        });

        $bill = $billplz->createBill($order);

        $order->update(['billplz_bill_id' => $bill['id']]);
        $order->payment()->create([
            'provider_bill_id' => $bill['id'],
            'amount_cents' => $order->total_cents,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        return redirect()->away($bill['url']);
    }

    public function render(): View
    {
        $cart = Cart::with('items.product.translations')->where('user_id', auth()->id())->first();

        return view('livewire.checkout', [
            'items' => $cart?->items ?? collect(),
            'totalCents' => $cart?->subtotalCents() ?? 0,
        ])->layout('layouts.authenticated');
    }
}
