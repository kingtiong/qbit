<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $fillable = [
        'user_id',
        'deposit_address_id',
        'deposit_session_id',
        'tx_hash',
        'block_number',
        'from_address',
        'to_address',
        'token_symbol',
        'token_contract',
        'token_decimals',
        'amount',
        'status',
        'detected_at',
        'credited_at',
        'wallet_transaction_id',
        'raw',
    ];

    protected $casts = [
        'block_number' => 'integer',
        'token_decimals' => 'integer',
        'amount' => 'decimal:2',
        'detected_at' => 'datetime',
        'credited_at' => 'datetime',
        'raw' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function depositAddress(): BelongsTo
    {
        return $this->belongsTo(DepositAddress::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(DepositSession::class, 'deposit_session_id');
    }
}
