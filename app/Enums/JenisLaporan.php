<?php

namespace App\Enums;

enum JenisLaporan: string
{
    case SptMasa = 'spt_masa';
    case SptTahunanPribadi = 'spt_tahunan_pribadi';
    case SptTahunanBadan = 'spt_tahunan_badan';

    public function label(): string
    {
        return match ($this) {
            self::SptMasa => 'SPT Masa',
            self::SptTahunanPribadi => 'SPT Tahunan Pribadi',
            self::SptTahunanBadan => 'SPT Tahunan Badan',
        };
    }
}
