<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slip extends Model
{
    protected $fillable = ['code', 'zone_id', 'sort_order', 'max_aforo_ft', 'status'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function boats(): HasMany
    {
        return $this->hasMany(Boat::class);
    }

    // Actualiza automáticamente el estado ocupado/libre según embarcaciones asignadas
    public function syncStatus(): void
    {
        if ($this->status === 'maintenance') {
            return;
        }
        $hasActiveBoats = $this->boats()->where('navigation_status', '!=', 'de_baja')->exists();
        $this->update(['status' => $hasActiveBoats ? 'occupied' : 'free']);
    }
}
