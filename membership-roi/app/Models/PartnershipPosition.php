<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnershipPosition extends Model
{
    protected $fillable = [
        'user_id',
        'partnership_package_id',
        'status',
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(PartnershipPackage::class, 'partnership_package_id');
    }
}
