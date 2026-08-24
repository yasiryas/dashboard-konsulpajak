<?php

namespace App\Enums;

enum JenisDokumen: string
{
    case BuktiPotong = 'bukti_potong';
    case Invoice = 'invoice';
    case Npwp = 'npwp';
    case LaporanKeuangan = 'laporan_keuangan';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::BuktiPotong => 'Bukti Potong',
            self::Invoice => 'Invoice',
            self::Npwp => 'NPWP',
            self::LaporanKeuangan => 'Laporan Keuangan',
            self::Lainnya => 'Lainnya',
        };
    }
}
