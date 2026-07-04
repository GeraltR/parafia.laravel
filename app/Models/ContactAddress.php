<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactAddress extends Model
{
    protected $fillable = [
        'footer_config_id',
        'street',
        'city',
        'post_code',
        'phone',
        'social',
    ];

    protected function casts(): array
    {
        return [
            'social' => 'boolean',
        ];
    }

    public function footerConfig(): BelongsTo
    {
        return $this->belongsTo(FooterConfig::class);
    }
}
