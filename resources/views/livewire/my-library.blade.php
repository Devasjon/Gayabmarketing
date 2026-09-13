<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('cart.library.title') }}</h1>

    @if ($entitlements->isEmpty())
        <p class="text-gray-600">{{ __('cart.library.empty') }}</p>
        <a href="{{ route('home') }}#products" class="text-brand-600 font-semibold">{{ __('cart.library.browse') }}</a>
    @else
        <div class="bg-white shadow rounded-lg divide-y">
            @foreach ($entitlements as $entitlement)
                <div class="p-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $entitlement->product->localizedName() }}</p>
                        <p class="text-sm text-gray-500">{{ __('cart.library.purchased_on', ['date' => $entitlement->granted_at->format('d M Y')]) }}</p>
                    </div>
                    <div class="flex gap-3">
                        @forelse ($entitlement->product->files as $file)
                            <a href="{{ route('library.download', [$entitlement, $file]) }}" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded font-semibold text-sm">
                                {{ __('cart.library.download') }} ({{ $file->version }})
                            </a>
                        @empty
                            <span class="text-sm text-gray-400">—</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
