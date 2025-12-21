<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GbpTier extends Model
{
    protected $fillable = [
        'tier',
        'unit_price',
        'total_units',
        'sold_units',
        'is_active',
    ];

    protected $casts = [
        'tier' => 'integer',
        'unit_price' => 'integer',
        'total_units' => 'integer',
        'sold_units' => 'integer',
        'is_active' => 'boolean',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(GbpPurchase::class, 'gbp_tier_id');
    }
}

