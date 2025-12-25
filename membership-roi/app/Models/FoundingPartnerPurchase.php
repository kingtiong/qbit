<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoundingPartnerPurchase extends Model
{
    public const PACKAGE_PRO = 'pro';
    public const PACKAGE_PRO_MAX = 'pro_max';

    protected $fillable = [
        'user_id',
        'package',
        'amount',
        'wallet_transaction_id',
        'purchased_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'purchased_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

