<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroButton extends Model
{
    protected $fillable = [
        'hero_id',
        'label',
        'href',
        'icon',
        'external',
        'text_color',
        'text_color_hover',
        'bg_color',
        'bg_color_hover',
    ];

    protected function casts(): array
    {
        return [
            'external' => 'boolean',
        ];
    }

    public function hero(): BelongsTo
    {
        return $this->belongsTo(Hero::class);
    }
}
