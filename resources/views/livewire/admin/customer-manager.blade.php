<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.customers.title') }}</h1>
    <x-admin.nav />

    <div class="bg-white shadow rounded-lg divide-y">
        @forelse ($customers as $customer)
            <div class="p-4 flex items-center justify-between text-sm">
                <div>
                    <p class="font-semibold text-gray-900">{{ $customer->name }}</p>
                    <p class="text-gray-500">{{ $customer->email }} · {{ __('admin.customers.joined') }} {{ $customer->created_at->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-gray-500 text-xs">{{ __('admin.customers.orders') }}</p>
                        <p class="font-semibold">{{ $customer->paid_orders_count }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 text-xs">{{ __('admin.customers.total_spent') }}</p>
                        <p class="font-semibold">RM{{ number_format(($customer->total_spent_cents ?? 0) / 100, 2) }}</p>
                    </div>
                    <button wire:click="view({{ $customer->id }})" class="text-brand-600 font-semibold">{{ __('admin.customers.view') }}</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">{{ __('admin.customers.empty') }}</p>
        @endforelse
    </div>

    {{ $customers->links() }}
</div>
