<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    public const TYPE_REGISTERED = 'registered';
    public const TYPE_COMMISSION = 'commission';

    protected $fillable = [
        'user_id',
        'type',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public static function forUser(int $userId, string $type): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'type' => $type],
            ['balance' => 0]
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
