<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoExtra extends Model
{
    protected $fillable = [
        'title',
        'description',
        'images',
        'progress_percent',
        'bank_account',
        'donation_url',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'active' => 'boolean',
        ];
    }
}
