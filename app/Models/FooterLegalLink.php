<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterLegalLink extends Model
{
    protected $fillable = [
        'footer_config_id',
        'label',
        'href',
    ];

    public function footerConfig(): BelongsTo
    {
        return $this->belongsTo(FooterConfig::class);
    }
}
