<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfoItem extends Model
{
    protected $fillable = [
        'valid_from',
        'valid_to',
        'title',
        'short_info',
        'description',
        'image',
        'progress_value',
        'progress_description',
        'information',
        'banner_text',
        'banner_font',
        'banner_text_color',
        'banner_bg_color',
        'banner_duration_seconds',
        'author_id',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_to' => 'date',
            'progress_value' => 'integer',
            'banner_duration_seconds' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
