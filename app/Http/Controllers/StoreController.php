<?php
namespace App\Http\Controllers;
use App\Models\Product;
class StoreController extends Controller {
    public function index() { return view('store.index', ['products'=>Product::published()->latest()->get()]); }
    public function show(Product $product) { abort_unless($product->status === 'published', 404); return view('store.show', compact('product')); }
}

