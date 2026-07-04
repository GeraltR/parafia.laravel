<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'primary_color',
        'secondary_color',
        'font_heading',
        'font_body',
        'title',
        'subtitle',
    ];
}
