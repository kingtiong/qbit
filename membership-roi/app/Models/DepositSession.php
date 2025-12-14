<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepositSession extends Model
{
    protected $fillable = [
        'user_id',
        'deposit_address_id',
        'currency',
        'network',
        'status',
        'reserved_until',
        'completed_at',
        'credited_amount',
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'completed_at' => 'datetime',
        'credited_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function depositAddress(): BelongsTo
    {
        return $this->belongsTo(DepositAddress::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
}
