<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FinanceDashboard extends Component
{
    public function mount(): void
    {
        $this->authorize('viewAny', Order::class);
    }

    public function render(): View
    {
        $paid = Order::where('status', 'paid');

        $totalRevenueCents = (clone $paid)->sum('total_cents');
        $thisMonthRevenueCents = (clone $paid)->where('paid_at', '>=', now()->startOfMonth())->sum('total_cents');
        $paidOrderCount = (clone $paid)->count();

        $dailyRevenue = (clone $paid)
            ->where('paid_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(paid_at) as day'), DB::raw('SUM(total_cents) as total'), DB::raw('COUNT(*) as orders'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('livewire.admin.finance-dashboard', [
            'totalRevenueCents' => $totalRevenueCents,
            'thisMonthRevenueCents' => $thisMonthRevenueCents,
            'paidOrderCount' => $paidOrderCount,
            'dailyRevenue' => $dailyRevenue,
        ])->layout('layouts.authenticated');
    }
}
