<?php

use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ProductManager;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'permission:admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/products', ProductManager::class)->name('products');
        Route::get('/categories', CategoryManager::class)->name('categories');
        Route::get('/orders', OrderManager::class)->name('orders');
    });
