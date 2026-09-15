<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->invoice, 404);

        $order->load(['items', 'invoice', 'user']);

        return view('store.invoice', ['order' => $order]);
    }
}
