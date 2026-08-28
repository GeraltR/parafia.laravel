<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssociationsConfig extends Model
{
    protected $fillable = [
        'name_font',
        'name_size',
    ];

    public function associations(): HasMany
    {
        return $this->hasMany(Association::class)->orderBy('order');
    }
}
