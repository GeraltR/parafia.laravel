<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hero extends Model
{
    protected $fillable = [
        'title',
        'title_width',
        'title_font',
        'title_v_align',
        'subtitle',
        'subtitle_width',
        'subtitle_font',
        'subtitle_v_align',
        'keynote',
        'keynote_width',
        'keynote_font',
        'keynote_v_align',
        'background_image',
    ];

    public function buttons(): HasMany
    {
        return $this->hasMany(HeroButton::class);
    }
}
