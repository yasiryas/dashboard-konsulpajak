<?php

namespace App\Enums;

enum StatusLaporan: string
{
    case Draft = 'draft';
    case MenungguDokumen = 'menunggu_dokumen';
    case Diproses = 'diproses';
    case Dilaporkan = 'dilaporkan';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::MenungguDokumen => 'Menunggu Dokumen',
            self::Diproses => 'Diproses',
            self::Dilaporkan => 'Dilaporkan',
            self::Selesai => 'Selesai',
        };
    }
}
