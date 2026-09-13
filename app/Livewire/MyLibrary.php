<?php

namespace App\Livewire;

use App\Models\Entitlement;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MyLibrary extends Component
{
    public function render(): View
    {
        $entitlements = Entitlement::with(['product.translations', 'product.files'])
            ->where('user_id', auth()->id())
            ->latest('granted_at')
            ->get();

        return view('livewire.my-library', ['entitlements' => $entitlements])
            ->layout('layouts.authenticated');
    }
}
