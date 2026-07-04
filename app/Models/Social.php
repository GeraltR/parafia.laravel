<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Social extends Model
{
    protected $table = 'social';

    protected $fillable = [
        'footer_config_id',
        'social_name',
        'social_link',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => 'boolean',
        ];
    }

    public function footerConfig(): BelongsTo
    {
        return $this->belongsTo(FooterConfig::class);
    }
}
