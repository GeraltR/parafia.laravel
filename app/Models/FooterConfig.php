<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FooterConfig extends Model
{
    protected $fillable = [
        'office_title',
        'office_note',
        'map_embed_url',
        'map_link',
        'copyright_text',
        'bg_color',
        'title_font',
        'title_size',
        'title_color',
    ];

    public function contactAddresses(): HasMany
    {
        return $this->hasMany(ContactAddress::class);
    }

    public function social(): HasMany
    {
        return $this->hasMany(Social::class);
    }

    public function officeHours(): HasMany
    {
        return $this->hasMany(OfficeHour::class);
    }

    public function legalLinks(): HasMany
    {
        return $this->hasMany(FooterLegalLink::class);
    }
}
