<?php

use App\Http\Controllers\Admin\FinanceExportController;
use App\Livewire\Admin\AuditLogManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\CustomerManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\FinanceDashboard;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Admin\TaskBoard;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'permission:admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/products', ProductManager::class)->name('products');
        Route::get('/categories', CategoryManager::class)->name('categories');
        Route::get('/orders', OrderManager::class)->name('orders');
        Route::get('/finance', FinanceDashboard::class)->name('finance');
        Route::get('/finance/export', FinanceExportController::class)->name('finance.export');
        Route::get('/tasks', TaskBoard::class)->name('tasks');
        Route::get('/customers', CustomerManager::class)->name('customers');
        Route::get('/audit-log', AuditLogManager::class)->name('audit-log');
    });
