<?php

namespace App\Livewire\Admin;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\CustomerNote;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerManager extends Component
{
    use WithPagination;

    public ?int $viewingId = null;

    #[Validate('required|string|max:2000')]
    public string $noteBody = '';

    public function mount(): void
    {
        $this->authorize(Permission::ViewCustomers->value);
    }

    public function view(int $userId): void
    {
        $this->viewingId = $userId;
    }

    public function back(): void
    {
        $this->viewingId = null;
        $this->reset('noteBody');
    }

    public function addNote(): void
    {
        $this->authorize(Permission::ManageCustomerNotes->value);
        $this->validate();

        CustomerNote::create([
            'user_id' => $this->viewingId,
            'author_id' => auth()->id(),
            'body' => $this->noteBody,
        ]);

        $this->reset('noteBody');
    }

    public function render(): View
    {
        if ($this->viewingId) {
            $customer = User::role(Role::Customer->value)
                ->with(['orders' => fn ($q) => $q->where('status', 'paid'), 'customerNotes.author'])
                ->findOrFail($this->viewingId);

            return view('livewire.admin.customer-detail', ['customer' => $customer])->layout('layouts.authenticated');
        }

        $customers = User::role(Role::Customer->value)
            ->withCount(['orders as paid_orders_count' => fn ($q) => $q->where('status', 'paid')])
            ->withSum(['orders as total_spent_cents' => fn ($q) => $q->where('status', 'paid')], 'total_cents')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.customer-manager', ['customers' => $customers])->layout('layouts.authenticated');
    }
}
