<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.products.title') }}</h1>
        @unless ($showForm)
            <button wire:click="create" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.products.new') }}</button>
        @endunless
    </div>
    <x-admin.nav />

    @if (session('status'))
        <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded">{{ session('status') }}</div>
    @endif

    @if ($showForm)
        <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-4">
            <h2 class="font-semibold text-lg">{{ $editingId ? __('admin.products.edit') : __('admin.products.new') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.name_en') }}</label>
                    <input type="text" wire:model.live="nameEn" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('nameEn') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.name_ms') }}</label>
                    <input type="text" wire:model="nameMs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('nameMs') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.description_en') }}</label>
                    <textarea wire:model="descriptionEn" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    @error('descriptionEn') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.description_ms') }}</label>
                    <textarea wire:model="descriptionMs" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    @error('descriptionMs') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.category') }}</label>
                    <select wire:model="categoryId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">—</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.price') }}</label>
                    <input type="text" wire:model="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.slug') }}</label>
                    <input type="text" wire:model="slug" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('slug') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.status') }}</label>
                    <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="draft">draft</option>
                        <option value="published">published</option>
                    </select>
                    @error('status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.cover') }}</label>
                    <input type="file" wire:model="cover" class="mt-1 block w-full text-sm">
                    @error('cover') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    @if ($cover) <img src="{{ $cover->temporaryUrl() }}" class="mt-2 h-20 rounded" alt=""> @endif
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.products.save') }}</button>
                <button type="button" wire:click="cancel" class="text-gray-500 text-sm">{{ __('admin.products.cancel') }}</button>
            </div>
        </form>
    @endif

    @if ($showForm && $editingId)
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <h2 class="font-semibold text-lg">{{ __('admin.products.files') }}</h2>

            <div class="divide-y">
                @forelse ($editingFiles as $file)
                    <div class="py-2 flex items-center justify-between text-sm">
                        <span>{{ $file->original_name }} ({{ $file->version }}, {{ number_format($file->size_bytes / 1024, 0) }} KB)</span>
                        <button wire:click="deleteFile({{ $file->id }})" wire:confirm="{{ __('admin.products.delete_file_confirm') }}" class="text-red-600 font-semibold">{{ __('admin.categories.delete') }}</button>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm py-2">{{ __('admin.products.no_files') }}</p>
                @endforelse
            </div>

            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.products.file_version') }}</label>
                    <input type="text" wire:model="newFileVersion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex-1">
                    <input type="file" wire:model="newFile" class="mt-1 block w-full text-sm">
                    @error('newFile') <span class="text-red-600 text-sm block">{{ $message }}</span> @enderror
                </div>
                <button type="button" wire:click="uploadFile" class="bg-ink text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.products.upload_file') }}</button>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg divide-y">
        @forelse ($products as $product)
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @if ($product->cover_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($product->cover_path) }}" class="w-12 h-12 rounded object-cover" alt="">
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900">{{ $product->translation('en')?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $product->category?->name }} · RM{{ number_format($product->price_cents / 100, 2) }} · {{ $product->status }}</p>
                    </div>
                </div>
                <div class="flex gap-3 text-sm">
                    <button wire:click="edit({{ $product->id }})" class="text-brand-600 font-semibold">{{ __('admin.products.edit') }}</button>
                    <button wire:click="delete({{ $product->id }})" wire:confirm="{{ __('admin.products.delete_confirm') }}" class="text-red-600 font-semibold">{{ __('admin.products.delete') }}</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">{{ __('admin.products.empty') }}</p>
        @endforelse
    </div>
</div>
