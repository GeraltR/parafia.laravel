<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = [
        'date',
        'title',
        'excerpt',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
