<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.finance.title') }}</h1>
        <a href="{{ route('admin.finance.export') }}" class="bg-ink text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.finance.export_csv') }}</a>
    </div>
    <x-admin.nav />

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">{{ __('admin.finance.total_revenue') }}</p>
            <p class="text-3xl font-bold text-brand-600">RM{{ number_format($totalRevenueCents / 100, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">{{ __('admin.finance.this_month') }}</p>
            <p class="text-3xl font-bold text-ink">RM{{ number_format($thisMonthRevenueCents / 100, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">{{ __('admin.finance.paid_orders') }}</p>
            <p class="text-3xl font-bold text-ink">{{ $paidOrderCount }}</p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <h2 class="font-semibold p-4 border-b">{{ __('admin.finance.last_30_days') }}</h2>
        <div class="divide-y">
            @forelse ($dailyRevenue as $row)
                <div class="p-4 flex items-center justify-between text-sm">
                    <span>{{ \Illuminate\Support\Carbon::parse($row->day)->format('d M Y') }}</span>
                    <span class="text-gray-500">{{ $row->orders }} {{ __('admin.orders.items') }}</span>
                    <span class="font-semibold">RM{{ number_format($row->total / 100, 2) }}</span>
                </div>
            @empty
                <p class="p-4 text-gray-500">{{ __('admin.finance.empty') }}</p>
            @endforelse
        </div>
    </div>
</div>
