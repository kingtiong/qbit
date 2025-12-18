<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoTrade extends Model
{
    protected $fillable = [
        'user_id',
        'trade_date',
        'symbol',
        'pair',
        'side',
        'risk_level',
        'liquidity_range_low',
        'liquidity_range_high',
        'pool_size_usd',
        'fee_tier_bps',
        'buy_price',
        'sell_price',
        'qty',
        'pnl',
        'pnl_pct',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'trade_date' => 'date',
        'side' => 'string',
        'risk_level' => 'string',
        'liquidity_range_low' => 'decimal:8',
        'liquidity_range_high' => 'decimal:8',
        'pool_size_usd' => 'decimal:2',
        'fee_tier_bps' => 'integer',
        'buy_price' => 'decimal:8',
        'sell_price' => 'decimal:8',
        'qty' => 'decimal:8',
        'pnl' => 'decimal:2',
        'pnl_pct' => 'decimal:4',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

