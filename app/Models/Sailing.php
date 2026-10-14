<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sailing extends Model
{
    protected $fillable = [
        'boat_id',
        'origin_slip_id',
        'return_slip_id',
        'destination',
        'departed_at',
        'expected_return_at',
        'returned_at',
        'user_id',
    ];

    protected $casts = [
        'departed_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }

    public function originSlip(): BelongsTo
    {
        return $this->belongsTo(Slip::class, 'origin_slip_id');
    }

    public function returnSlip(): BelongsTo
    {
        return $this->belongsTo(Slip::class, 'return_slip_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
