<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    public $timestamps = false;

    protected $fillable = ['entitlement_id', 'product_file_id', 'ip_address', 'user_agent', 'downloaded_at'];

    protected function casts(): array
    {
        return ['downloaded_at' => 'datetime'];
    }

    /**
     * @return BelongsTo<Entitlement, $this>
     */
    public function entitlement(): BelongsTo
    {
        return $this->belongsTo(Entitlement::class);
    }

    /**
     * @return BelongsTo<ProductFile, $this>
     */
    public function productFile(): BelongsTo
    {
        return $this->belongsTo(ProductFile::class);
    }
}
