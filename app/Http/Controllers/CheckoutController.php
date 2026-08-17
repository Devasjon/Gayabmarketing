<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use App\Services\BillplzService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class CheckoutController extends Controller {
    public function store(Request $request, BillplzService $billplz) {
        abort_unless(config('services.billplz.checkout_enabled'), 503, 'Checkout is not open yet.');
        $data = $request->validate(['product_id'=>'required|exists:products,id','name'=>'required|max:120','email'=>'required|email|max:180','phone'=>'required|max:30']);
        $product = Product::published()->findOrFail($data['product_id']);
        $order = Order::create(['reference'=>'GBM-'.now()->format('ymd').'-'.strtoupper(Str::random(6)),'product_id'=>$product->id,'customer_name'=>$data['name'],'customer_email'=>$data['email'],'customer_phone'=>$data['phone'],'amount_cents'=>$product->price_cents,'status'=>'pending','download_token'=>Str::random(64)]);
        $bill = $billplz->createBill($order);
        $order->update(['billplz_bill_id'=>$bill['id']]);
        return redirect()->away($bill['url']);
    }
}

