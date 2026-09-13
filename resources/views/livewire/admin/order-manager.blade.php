<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.orders.title') }}</h1>
    <x-admin.nav />

    <div class="bg-white shadow rounded-lg divide-y">
        @forelse ($orders as $order)
            <div class="p-4 flex items-center justify-between text-sm">
                <div>
                    <p class="font-semibold text-gray-900">{{ $order->reference }}</p>
                    <p class="text-gray-500">{{ $order->user?->name }} · {{ $order->items->count() }} {{ __('admin.orders.items') }} · RM{{ number_format($order->total_cents / 100, 2) }}</p>
                </div>
                <span class="px-2 py-1 rounded text-xs font-semibold
                    {{ $order->status->value === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $order->status->value }}
                </span>
            </div>
        @empty
            <p class="p-4 text-gray-500">{{ __('admin.orders.empty') }}</p>
        @endforelse
    </div>

    {{ $orders->links() }}
</div>
