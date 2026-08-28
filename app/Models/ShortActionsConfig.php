<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortActionsConfig extends Model
{
    protected $fillable = [
        'title_font',
        'title_size',
        'title_color',
        'subtitle_font',
        'subtitle_size',
        'subtitle_color',
        'bg_color',
        'bg_color_hover',
    ];
}
