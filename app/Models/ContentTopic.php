<?php

namespace App\Models;

use App\Enums\ContentPageSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentTopic extends Model
{
    protected $fillable = [
        'page',
        'icon_url',
        'title',
        'content',
        'visible_from',
        'author_id',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'page' => ContentPageSlug::class,
            'visible_from' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNull('visible_from')->orWhere('visible_from', '<=', now());
        });
    }
}
