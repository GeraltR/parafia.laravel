<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeHour extends Model
{
    protected $fillable = [
        'footer_config_id',
        'day',
        'hours_on',
        'hours_end',
    ];

    public function footerConfig(): BelongsTo
    {
        return $this->belongsTo(FooterConfig::class);
    }
}
