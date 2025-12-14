<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investment extends Model
{
    protected $fillable = [
        'user_id',
        'investment_package_id',
        'currency',
        'amount',
        'status',
        'started_on',
        'last_accrued_on',
        'capped_on',
        'capped_reason',
        'total_earned',
        'max_return_amount',
    ];

    protected $casts = [
        'started_on' => 'date',
        'last_accrued_on' => 'date',
        'capped_on' => 'date',
        'amount' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'max_return_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(InvestmentPackage::class, 'investment_package_id');
    }

    public function roiEarnings(): HasMany
    {
        return $this->hasMany(RoiEarning::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
