<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class InvestmentPackage extends Model
{
    protected $fillable = [
        'label',
        'summary',
        'benefits',
        'code',
        'currency',
        'amount',
        'daily_qos_amount',
        'max_return_multiplier',
        'is_active',
        'total_units',
        'sold_units',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_qos_amount' => 'decimal:2',
        'max_return_multiplier' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean',
        'total_units' => 'integer',
        'sold_units' => 'integer',
    ];

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
}
