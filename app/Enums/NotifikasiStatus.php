<?php

namespace App\Enums;

enum NotifikasiStatus: string
{
    case Pending = 'pending';
    case Terkirim = 'terkirim';
    case Gagal = 'gagal';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Terkirim => 'Terkirim',
            self::Gagal => 'Gagal',
        };
    }
}
