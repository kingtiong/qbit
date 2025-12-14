<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnershipPackage extends Model
{
    protected $fillable = [
        'level',
        'code',
        'currency',
        'amount',
        'group_percent',
        'global_denom',
        'holder_limit',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'group_percent' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(PartnershipPosition::class);
    }
}
