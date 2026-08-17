<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Product extends Model {
    protected $fillable = ['name_en','name_bm','slug','description_en','description_bm','category','price_cents','status','file_path','cover_path'];
    protected function casts(): array { return ['price_cents'=>'integer']; }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function scopePublished($query) { return $query->where('status','published'); }
}

