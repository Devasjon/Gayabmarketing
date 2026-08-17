<?php
use App\Http\Controllers\BillplzController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [StoreController::class, 'show'])->name('products.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/{order}/return', [BillplzController::class, 'redirect'])->name('billplz.redirect');
Route::post('/billplz/callback', [BillplzController::class, 'callback'])->name('billplz.callback');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/terms', 'legal.terms')->name('terms');

