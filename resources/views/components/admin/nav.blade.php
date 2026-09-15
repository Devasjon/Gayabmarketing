<nav class="flex gap-6 border-b border-gray-200 -mb-px overflow-x-auto whitespace-nowrap">
    <a href="{{ route('admin.dashboard') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
        {{ __('admin.nav.dashboard') }}
    </a>
    <a href="{{ route('admin.products') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.products') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
        {{ __('admin.nav.products') }}
    </a>
    <a href="{{ route('admin.categories') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.categories') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
        {{ __('admin.nav.categories') }}
    </a>
    @can('viewAny', \App\Models\Order::class)
        <a href="{{ route('admin.orders') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.orders') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
            {{ __('admin.nav.orders') }}
        </a>
        <a href="{{ route('admin.finance') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.finance') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
            {{ __('admin.nav.finance') }}
        </a>
    @endcan
    @can('viewAny', \App\Models\Task::class)
        <a href="{{ route('admin.tasks') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.tasks') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
            {{ __('admin.nav.tasks') }}
        </a>
    @endcan
    @can(\App\Enums\Permission::ViewCustomers->value)
        <a href="{{ route('admin.customers') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.customers') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
            {{ __('admin.nav.customers') }}
        </a>
    @endcan
    @can(\App\Enums\Permission::ViewAuditLogs->value)
        <a href="{{ route('admin.audit-log') }}" class="pb-3 text-sm font-semibold {{ request()->routeIs('admin.audit-log') ? 'text-brand-600 border-b-2 border-brand-600' : 'text-gray-500' }}">
            {{ __('admin.nav.audit') }}
        </a>
    @endcan
</nav>
