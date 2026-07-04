<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    protected $fillable = [
        'date',
        'time',
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
