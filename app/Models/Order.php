<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Order extends Model {
    protected $fillable = ['reference','product_id','customer_name','customer_email','customer_phone','amount_cents','status','billplz_bill_id','paid_at','download_token'];
    protected function casts(): array { return ['paid_at'=>'datetime','amount_cents'=>'integer']; }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}

