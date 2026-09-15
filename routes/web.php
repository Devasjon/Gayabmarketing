<?php

use App\Http\Controllers\BillplzController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Livewire\CartPage;
use App\Livewire\Checkout;
use App\Livewire\MyLibrary;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [StoreController::class, 'show'])->name('products.show');
Route::post('/billplz/callback', [BillplzController::class, 'callback'])->middleware('throttle:60,1')->name('billplz.callback');
Route::get('/locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/refund-policy', 'legal.refund')->name('refund');
Route::view('/digital-product-license', 'legal.license')->name('license');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cart', CartPage::class)->name('cart.index');
    Route::get('/checkout', Checkout::class)->name('checkout.index');
    Route::get('/checkout/{order}/return', [BillplzController::class, 'redirect'])->name('billplz.redirect');

    Route::get('/library', MyLibrary::class)->name('library.index');
    Route::get('/library/download/{entitlement}/{productFile}', DownloadController::class)->name('library.download');

    Route::get('/orders/{order}/invoice', InvoiceController::class)->name('orders.invoice');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
