<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'path',
        'visitor_hash',
        'referrer',
    ];
}
