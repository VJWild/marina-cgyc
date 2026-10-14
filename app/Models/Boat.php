<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Boat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'matricula',
        'owner_name',
        'has_member',
        'member_action_number',
        'member_name',
        'member_dni',
        'member_phone',
        'member_email',
        'eslora_ft',
        'arqueo_bruto',
        'crew_capacity',
        'aforo_ft',
        'slip_id',
        'navigation_status',
        'deactivated_reason',
    ];

    protected $casts = [
        'has_member' => 'boolean',
        'eslora_ft' => 'decimal:2',
        'arqueo_bruto' => 'decimal:2',
        'crew_capacity' => 'integer',
        'aforo_ft' => 'decimal:2',
    ];

    public function slip(): BelongsTo
    {
        return $this->belongsTo(Slip::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BoatDocument::class);
    }

    public function sailings(): HasMany
    {
        return $this->hasMany(Sailing::class);
    }

    public function activeSailing(): HasOne
    {
        return $this->hasOne(Sailing::class)->whereNull('returned_at')->latestOfMany('departed_at');
    }
}
