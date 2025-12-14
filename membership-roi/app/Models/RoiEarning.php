<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoiEarning extends Model
{
    protected $fillable = [
        'investment_id',
        'user_id',
        'date',
        'rate',
        'amount',
        'wallet_transaction_id',
    ];

    protected $casts = [
        'date' => 'date',
        'rate' => 'decimal:5',
        'amount' => 'decimal:2',
    ];

    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }
}
