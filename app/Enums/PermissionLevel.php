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

    public function canWriteContent(): bool
    {
        return $this->value >= self::Editor->value;
    }

    public function canWriteSite(): bool
    {
        return $this->value >= self::Administrator->value;
    }

    public function canWriteManagement(): bool
    {
        return $this->value >= self::Supervisor->value;
    }
}
