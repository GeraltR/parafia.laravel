<?php

namespace App\Enums;

enum PermissionLevel: int
{
    case Viewer = 0;
    case Editor = 1;
    case Administrator = 3;
    case Supervisor = 7;

    public function label(): string
    {
        return match ($this) {
            self::Viewer => 'Przeglądanie',
            self::Editor => 'Redaktor',
            self::Administrator => 'Administrator',
            self::Supervisor => 'Supervisor',
        };
    }

    public function canWrite(): bool
    {
        return $this->value >= self::Administrator->value;
    }
}
