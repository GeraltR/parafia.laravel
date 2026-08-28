<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pastor extends Model
{
    protected $fillable = [
        'mass_and_pastor_section_id',
        'position',
        'full_name',
        'photo_url',
        'duties',
        'order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(MassAndPastorSection::class, 'mass_and_pastor_section_id');
    }
}
