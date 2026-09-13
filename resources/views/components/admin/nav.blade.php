<nav class="flex gap-6 border-b border-gray-200 -mb-px">
    <a href="{{ route('admin.dashboard') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
        {{ __('admin.nav.dashboard') }}
    </a>
    <a href="{{ route('admin.products') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.products') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
        {{ __('admin.nav.products') }}
    </a>
    <a href="{{ route('admin.categories') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.categories') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
        {{ __('admin.nav.categories') }}
    </a>
</nav>
