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

