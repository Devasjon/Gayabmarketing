<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.dashboard.title') }}</h1>
    <x-admin.nav />

    <p class="text-gray-600">{{ __('admin.dashboard.welcome', ['name' => auth()->user()->name]) }}</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">{{ __('admin.dashboard.total_products') }}</p>
            <p class="text-3xl font-bold text-ink">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">{{ __('admin.dashboard.published_products') }}</p>
            <p class="text-3xl font-bold text-brand-600">{{ $publishedProducts }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">{{ __('admin.dashboard.total_categories') }}</p>
            <p class="text-3xl font-bold text-ink">{{ $totalCategories }}</p>
        </div>
    </div>
</div>
