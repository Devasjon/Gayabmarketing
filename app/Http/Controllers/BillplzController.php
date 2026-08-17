<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Services\BillplzService;
use Illuminate\Http\Request;
class BillplzController extends Controller {
    public function callback(Request $request, BillplzService $billplz) {
        abort_unless($billplz->validSignature($request->all()), 403);
        $this->markPaid($request->input('id'), $request->boolean('paid'));
        return response('OK');
    }
    public function redirect(Request $request, Order $order, BillplzService $billplz) {
        abort_unless($billplz->validRedirectSignature($request->all()), 403);
        $this->markPaid($request->input('billplz.id'), $request->boolean('billplz.paid'));
        return view('store.receipt', ['order'=>$order->fresh('product')]);
    }
    private function markPaid(?string $billId, bool $paid): void {
        if (!$paid || !$billId) return;
        Order::where('billplz_bill_id',$billId)->where('status','pending')->update(['status'=>'paid','paid_at'=>now()]);
    }
}

