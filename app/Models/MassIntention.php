<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MassIntention extends Model
{
    protected $fillable = [
        'date',
        'time',
        'intention',
        'is_holiday',
        'day_description',
        'author_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_holiday' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
