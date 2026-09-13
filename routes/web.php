<?php

use App\Http\Controllers\BillplzController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [StoreController::class, 'show'])->name('products.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/{order}/return', [BillplzController::class, 'redirect'])->name('billplz.redirect');
Route::post('/billplz/callback', [BillplzController::class, 'callback'])->name('billplz.callback');
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
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
