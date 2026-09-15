<?php

namespace App\Livewire\Admin;

use App\Enums\Role;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TaskBoard extends Component
{
    public ?int $editingId = null;

    #[Validate('required|string|max:180')]
    public string $title = '';

    #[Validate('nullable|string|max:2000')]
    public string $description = '';

    #[Validate('nullable|date')]
    public string $dueAt = '';

    #[Validate('nullable|exists:users,id')]
    public ?int $assignedTo = null;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorize('viewAny', Task::class);
    }

    public function create(): void
    {
        $this->authorize('create', Task::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Task $task): void
    {
        $this->authorize('update', $task);

        $this->editingId = $task->id;
        $this->title = $task->title;
        $this->description = (string) $task->description;
        $this->dueAt = $task->due_at?->format('Y-m-d') ?? '';
        $this->assignedTo = $task->assigned_to;
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'due_at' => $this->dueAt ?: null,
            'assigned_to' => $this->assignedTo,
        ];

        if ($this->editingId) {
            $task = Task::findOrFail($this->editingId);
            $this->authorize('update', $task);
            $task->update($data);
        } else {
            $this->authorize('create', Task::class);
            Task::create($data + ['status' => TaskStatus::Todo->value, 'created_by' => auth()->id()]);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(Task $task): void
    {
        $this->authorize('delete', $task);
        $task->delete();
    }

    public function moveTo(Task $task, string $status): void
    {
        $this->authorize('update', $task);
        $task->update(['status' => $status]);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'description', 'dueAt', 'assignedTo']);
        $this->resetValidation();
    }

    public function render(): View
    {
        $tasks = Task::with(['assignee'])->orderBy('position')->orderByDesc('created_at')->get()->groupBy('status');

        return view('livewire.admin.task-board', [
            'columns' => TaskStatus::cases(),
            'tasksByStatus' => $tasks,
            'staff' => User::role(array_map(fn (Role $role) => $role->value, Role::staffRoles()))->orderBy('name')->get(),
            'dueSoon' => Task::whereNotNull('due_at')
                ->where('status', '!=', TaskStatus::Done->value)
                ->where('due_at', '<=', now()->addDays(3))
                ->orderBy('due_at')
                ->get(),
        ])->layout('layouts.authenticated');
    }
}
