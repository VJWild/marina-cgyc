<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['name', 'color_key', 'hex_code', 'display_order'];

    public function slips(): HasMany
    {
        return $this->hasMany(Slip::class)->orderBy('sort_order');
    }
}
