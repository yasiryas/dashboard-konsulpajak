<?php

namespace App\Enums;

enum StatusBayar: string
{
    case BelumDibayar = 'belum_dibayar';
    case Lunas = 'lunas';
    case Batal = 'batal';

    public function label(): string
    {
        return match ($this) {
            self::BelumDibayar => 'Belum Dibayar',
            self::Lunas => 'Lunas',
            self::Batal => 'Batal',
        };
    }
}
