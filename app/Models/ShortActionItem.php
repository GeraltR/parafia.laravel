<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortActionItem extends Model
{
    protected $fillable = [
        'icon',
        'title',
        'description',
        'href',
        'external',
    ];

    protected function casts(): array
    {
        return [
            'external' => 'boolean',
        ];
    }
}
