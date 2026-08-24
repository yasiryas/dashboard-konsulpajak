<?php

namespace App\Enums;

enum JenisKlien: string
{
    case Dokter = 'dokter';
    case Pengacara = 'pengacara';
    case Notaris = 'notaris';
    case Umkm = 'umkm';
    case Pt = 'pt';
    case Cv = 'cv';
    case Yayasan = 'yayasan';

    public function label(): string
    {
        return match ($this) {
            self::Dokter => 'Dokter',
            self::Pengacara => 'Pengacara',
            self::Notaris => 'Notaris',
            self::Umkm => 'UMKM',
            self::Pt => 'PT',
            self::Cv => 'CV',
            self::Yayasan => 'Yayasan',
        };
    }
}
