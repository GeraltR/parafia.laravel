<?php

namespace App\Enums;

enum ContentPageSlug: string
{
    case Sakramenty = 'sakramenty';
    case Parafia = 'parafia';
    case Liturgia = 'liturgia';

    public function maxTopics(): ?int
    {
        return match ($this) {
            self::Sakramenty => 7,
            default => null,
        };
    }
}
