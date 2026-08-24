<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Client = 'klien';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Client => 'Klien',
        };
    }
}
