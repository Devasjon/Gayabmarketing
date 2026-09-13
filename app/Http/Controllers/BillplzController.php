<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BillplzService;
use App\Services\PaymentProcessor;
use Illuminate\Http\Request;

class BillplzController extends Controller
{
    public function callback(Request $request, BillplzService $billplz, PaymentProcessor $processor)
    {
        $payload = $request->all();
        $signatureValid = $billplz->validSignature($payload);
        $billId = $request->input('id');

        $processor->handle(
            order: $billId ? Order::where('billplz_bill_id', $billId)->first() : null,
            billId: $billId,
            paid: $request->boolean('paid'),
            signatureValid: $signatureValid,
            reportedAmountCents: $request->filled('amount') ? (int) $request->input('amount') : null,
            payload: $payload,
            type: 'callback',
        );

        return response('OK');
    }

    public function redirect(Request $request, Order $order, BillplzService $billplz, PaymentProcessor $processor)
    {
        $payload = $request->all();
        $signatureValid = $billplz->validRedirectSignature($payload);

        $processor->handle(
            order: $order,
            billId: $request->input('billplz.id'),
            paid: $request->boolean('billplz.paid'),
            signatureValid: $signatureValid,
            reportedAmountCents: null,
            payload: $payload,
            type: 'redirect',
        );

        return view('store.receipt', ['order' => $order->fresh(['items', 'payment'])]);
    }
}
