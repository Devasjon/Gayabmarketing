<?php
namespace App\Services;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
class BillplzService {
    public function createBill(Order $order): array {
        return Http::withBasicAuth(config('services.billplz.api_key'),'')
            ->asForm()->post(config('services.billplz.endpoint').'/v3/bills',[
                'collection_id'=>config('services.billplz.collection_id'),
                'email'=>$order->customer_email,'mobile'=>$order->customer_phone,'name'=>$order->customer_name,
                'amount'=>$order->amount_cents,'callback_url'=>route('billplz.callback'),
                'redirect_url'=>route('billplz.redirect',$order),'description'=>'Gaya B Marketing — '.$order->product->name_en,
                'reference_1_label'=>'Order','reference_1'=>$order->reference,
            ])->throw()->json();
    }
    public function validSignature(array $payload): bool {
        $signature = $payload['x_signature'] ?? '';
        unset($payload['x_signature']);
        ksort($payload);
        $source = collect($payload)->map(fn($v,$k)=>$k.$v)->implode('|');
        return hash_equals(hash_hmac('sha256',$source,config('services.billplz.x_signature')), $signature);
    }
    public function validRedirectSignature(array $payload): bool {
        $data = $payload['billplz'] ?? [];
        $source = 'billplzid'.($data['id']??'').'|billplzpaid_at'.($data['paid_at']??'').'|billplzpaid'.($data['paid']??'').'|billplzx_signature'.($data['x_signature']??'');
        return !empty($data['x_signature']) && hash_equals(hash_hmac('sha256',$source,config('services.billplz.x_signature')), $data['x_signature']);
    }
}

