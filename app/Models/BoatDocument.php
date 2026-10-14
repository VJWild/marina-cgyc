<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoatDocument extends Model
{
    protected $fillable = [
        'boat_id',
        'document_type',
        'document_number',
        'issued_at',
        'expires_at',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }
}
