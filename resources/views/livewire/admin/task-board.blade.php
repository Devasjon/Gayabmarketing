<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.tasks.title') }}</h1>
        @unless ($showForm)
            <button wire:click="create" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.tasks.new') }}</button>
        @endunless
    </div>
    <x-admin.nav />

    @if ($showForm)
        <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-4">
            <h2 class="font-semibold text-lg">{{ $editingId ? __('admin.tasks.edit') : __('admin.tasks.new') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.tasks.name') }}</label>
                    <input type="text" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.tasks.description') }}</label>
                    <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.tasks.due_date') }}</label>
                    <input type="date" wire:model="dueAt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.tasks.assignee') }}</label>
                    <select wire:model="assignedTo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">{{ __('admin.tasks.unassigned') }}</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded font-semibold text-sm">{{ __('admin.tasks.save') }}</button>
                <button type="button" wire:click="cancel" class="text-gray-500 text-sm">{{ __('admin.tasks.cancel') }}</button>
            </div>
        </form>
    @endif

    @if ($dueSoon->isNotEmpty())
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-sm mb-2">{{ __('admin.tasks.due_soon') }}</h2>
            <ul class="text-sm space-y-1">
                @foreach ($dueSoon as $task)
                    <li class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                        {{ $task->title }} — {{ $task->due_at->format('d M Y') }}
                        @if ($task->isOverdue()) ({{ __('admin.tasks.overdue') }}) @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach ($columns as $status)
            <div class="bg-gray-50 rounded-lg p-3" wire:key="col-{{ $status->value }}">
                <h2 class="font-semibold text-sm mb-3 text-gray-700">{{ $status->label() }} ({{ ($tasksByStatus[$status->value] ?? collect())->count() }})</h2>
                <div class="space-y-3">
                    @forelse ($tasksByStatus[$status->value] ?? [] as $task)
                        <div class="bg-white rounded-lg shadow p-3 text-sm space-y-2" wire:key="task-{{ $task->id }}">
                            <p class="font-semibold {{ $task->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">{{ $task->title }}</p>
                            @if ($task->due_at)
                                <p class="text-xs text-gray-500">{{ $task->due_at->format('d M Y') }}</p>
                            @endif
                            @if ($task->assignee)
                                <p class="text-xs text-gray-500">{{ $task->assignee->name }}</p>
                            @endif
                            <div class="flex items-center justify-between pt-1">
                                <select wire:change="moveTo({{ $task->id }}, $event.target.value)" class="text-xs rounded border-gray-300">
                                    <option value="" disabled selected>{{ __('admin.tasks.move_to') }}</option>
                                    @foreach ($columns as $target)
                                        @if ($target !== $status)
                                            <option value="{{ $target->value }}">{{ $target->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $task->id }})" class="text-brand-600 font-semibold text-xs">{{ __('admin.actions.edit') }}</button>
                                    <button wire:click="delete({{ $task->id }})" wire:confirm="{{ __('admin.tasks.delete_confirm') }}" class="text-red-600 font-semibold text-xs">{{ __('admin.tasks.delete') }}</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-xs">—</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
