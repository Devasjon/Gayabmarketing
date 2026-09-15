<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Order::class);

        $orders = Order::with(['user', 'items', 'payment'])
            ->where('status', 'paid')
            ->orderBy('paid_at')
            ->get();

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference', 'Paid At', 'Customer', 'Email', 'Items', 'Subtotal (RM)', 'Total (RM)', 'Billplz Bill ID']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->reference,
                    $order->paid_at?->format('Y-m-d H:i:s'),
                    $order->user?->name,
                    $order->user?->email,
                    $order->items->pluck('product_name')->implode('; '),
                    number_format($order->subtotal_cents / 100, 2),
                    number_format($order->total_cents / 100, 2),
                    $order->billplz_bill_id,
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, 'gayabmarketing-revenue-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
