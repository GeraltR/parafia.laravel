<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MassAndPastorSection extends Model
{
    protected $fillable = [
        'position_font',
        'position_size',
        'position_color',
        'name_font',
        'name_size',
        'name_color',
    ];

    public function massTimes(): HasMany
    {
        return $this->hasMany(MassTime::class)->orderBy('order');
    }

    public function pastors(): HasMany
    {
        return $this->hasMany(Pastor::class)->orderBy('order');
    }
}
