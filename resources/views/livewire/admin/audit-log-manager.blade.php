<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.audit.title') }}</h1>
    <x-admin.nav />

    <div class="bg-white shadow rounded-lg divide-y">
        @forelse ($logs as $log)
            <div class="p-4 text-sm">
                <div class="flex items-center justify-between">
                    <p>
                        <span class="font-semibold">{{ $log->user?->name ?? __('admin.audit.system') }}</span>
                        {{ __('admin.audit.'.$log->event, ['model' => class_basename($log->auditable_type), 'id' => $log->auditable_id]) }}
                    </p>
                    <span class="text-gray-400 text-xs">{{ $log->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if ($log->ip_address)
                    <p class="text-xs text-gray-400 mt-1">{{ $log->ip_address }}</p>
                @endif
            </div>
        @empty
            <p class="p-4 text-gray-500">{{ __('admin.audit.empty') }}</p>
        @endforelse
    </div>

    {{ $logs->links() }}
</div>
