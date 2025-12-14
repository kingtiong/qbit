<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepositAddress extends Model
{
    protected $fillable = [
        'chain',
        'address',
        'is_active',
        'last_assigned_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_assigned_at' => 'datetime',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(DepositSession::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
}
