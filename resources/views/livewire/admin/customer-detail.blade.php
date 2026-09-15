<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <button wire:click="back" class="text-brand-600 font-semibold text-sm">{{ __('admin.customers.back') }}</button>

    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h1>
        <p class="text-gray-500 text-sm">{{ $customer->email }} · {{ __('admin.customers.joined') }} {{ $customer->created_at->format('d M Y') }}</p>
        <div class="flex gap-8 mt-4">
            <div>
                <p class="text-gray-500 text-xs">{{ __('admin.customers.orders') }}</p>
                <p class="font-semibold text-lg">{{ $customer->orders->count() }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">{{ __('admin.customers.total_spent') }}</p>
                <p class="font-semibold text-lg">RM{{ number_format($customer->orders->sum('total_cents') / 100, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 space-y-4">
        <h2 class="font-semibold">{{ __('admin.customers.notes') }}</h2>

        <form wire:submit="addNote" class="space-y-2">
            <textarea wire:model="noteBody" rows="3" placeholder="{{ __('admin.customers.note_placeholder') }}" class="block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            @error('noteBody') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.customers.add_note') }}</button>
        </form>

        <div class="divide-y">
            @forelse ($customer->customerNotes->sortByDesc('created_at') as $note)
                <div class="py-3 text-sm">
                    <p class="text-gray-800">{{ $note->body }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $note->author->name }} · {{ $note->created_at->format('d M Y, H:i') }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-sm py-3">{{ __('admin.customers.no_notes') }}</p>
            @endforelse
        </div>
    </div>
</div>
