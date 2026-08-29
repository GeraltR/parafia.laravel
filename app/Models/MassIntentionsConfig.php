<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MassIntentionsConfig extends Model
{
    protected $fillable = [
        'holiday_described_color',
        'holiday_plain_color',
        'weekday_color',
    ];
}
