<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name_en', 'name_bm', 'slug', 'description_en', 'description_bm', 'category', 'price_cents', 'status', 'file_path', 'cover_path'];

    protected function casts(): array
    {
        return ['price_cents' => 'integer'];
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function localizedName(): string
    {
        return app()->getLocale() === 'ms' && $this->name_bm ? $this->name_bm : $this->name_en;
    }

    public function localizedDescription(): string
    {
        return app()->getLocale() === 'ms' && $this->description_bm ? $this->description_bm : $this->description_en;
    }
}
