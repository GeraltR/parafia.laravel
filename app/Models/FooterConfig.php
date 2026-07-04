<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FooterConfig extends Model
{
    protected $fillable = [
        'office_note',
        'map_embed_url',
        'map_link',
        'copyright_text',
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
