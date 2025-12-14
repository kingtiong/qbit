<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoiRate extends Model
{
    protected $fillable = [
        'date',
        'rate',
        'set_by_user_id',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'rate' => 'decimal:5',
    ];

    public function setBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by_user_id');
    }
}
