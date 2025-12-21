<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GbpPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'gbp_tier_id',
        'units',
        'unit_price',
        'total_amount',
        'wallet_transaction_id',
        'purchased_at',
    ];

    protected $casts = [
        'units' => 'integer',
        'unit_price' => 'integer',
        'total_amount' => 'integer',
        'purchased_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(GbpTier::class, 'gbp_tier_id');
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }
}

