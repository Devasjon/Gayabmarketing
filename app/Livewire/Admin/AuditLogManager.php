<?php

namespace App\Livewire\Admin;

use App\Enums\Permission;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogManager extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize(Permission::ViewAuditLogs->value);
    }

    public function render(): View
    {
        return view('livewire.admin.audit-log-manager', [
            'logs' => AuditLog::with('user')->latest('created_at')->paginate(30),
        ])->layout('layouts.authenticated');
    }
}
