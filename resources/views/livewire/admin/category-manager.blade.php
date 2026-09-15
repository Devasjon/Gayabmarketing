<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.categories.title') }}</h1>
    <x-admin.nav />

    @if (session('status'))
        <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 text-red-700 text-sm px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <form wire:submit="save" class="bg-white shadow rounded-lg p-6 flex gap-4 items-start">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">{{ __('admin.categories.name') }}</label>
            <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="pt-6 flex gap-2">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.categories.save') }}</button>
            @if ($editingId)
                <button type="button" wire:click="cancel" class="text-gray-500 text-sm">{{ __('product.back') }}</button>
            @endif
        </div>
    </form>

    <div class="bg-white shadow rounded-lg divide-y">
        @forelse ($categories as $category)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                    <p class="text-sm text-gray-500">{{ $category->products_count }} {{ __('admin.dashboard.total_products') }}</p>
                </div>
                <div class="flex gap-3 text-sm">
                    <button wire:click="edit({{ $category->id }})" class="text-brand-600 font-semibold">{{ __('admin.actions.edit') }}</button>
                    <button wire:click="delete({{ $category->id }})" wire:confirm="{{ __('admin.categories.delete_confirm') }}" class="text-red-600 font-semibold">{{ __('admin.categories.delete') }}</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">{{ __('admin.categories.empty') }}</p>
        @endforelse
    </div>
</div>
