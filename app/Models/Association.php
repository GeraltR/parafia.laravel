<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Association extends Model
{
    protected $fillable = [
        'associations_config_id',
        'name',
        'image_url',
        'link',
        'order',
    ];

    public function config(): BelongsTo
    {
        return $this->belongsTo(AssociationsConfig::class, 'associations_config_id');
    }
}
