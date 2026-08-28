<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MassTime extends Model
{
    protected $fillable = [
        'mass_and_pastor_section_id',
        'label',
        'hours',
        'note',
        'order',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(MassAndPastorSection::class, 'mass_and_pastor_section_id');
    }
}
