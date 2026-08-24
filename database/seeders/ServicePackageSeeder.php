<?php

namespace Database\Seeders;

use App\Enums\JenisKlien;
use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class ServicePackageSeeder extends Seeder
{
    public function run(): void
    {
        // Harga sesuai pricelist ahmadsandi.com/layanan (harga reguler, non-promo).
        $fiturProfesi = [
            'spt_masa' => 12,
            'bukti_potong_pph21' => true,
            'optimasi_nppn_pembukuan' => true,
            'konsultasi_wa' => true,
        ];

        $fiturBadan = $fiturProfesi + [
            'laporan_keuangan' => true,
        ];

        $paket = [
            ['Jasa Pajak Bulanan Profesi', JenisKlien::Dokter, 750000, $fiturProfesi],
            ['Jasa Pajak Bulanan Profesi', JenisKlien::Pengacara, 1000000, $fiturProfesi],
            ['Jasa Pajak Bulanan Profesi', JenisKlien::Notaris, 1000000, $fiturProfesi],
            ['Jasa Pajak Bulanan Badan', JenisKlien::Umkm, 500000, $fiturBadan],
            ['Jasa Pajak Bulanan Badan', JenisKlien::Pt, 2500000, $fiturBadan],
            ['Jasa Pajak Bulanan Badan', JenisKlien::Cv, 2000000, $fiturBadan],
            // Yayasan tidak tertulis eksplisit di pricelist; disamakan dengan tarif CV.
            ['Jasa Pajak Bulanan Badan', JenisKlien::Yayasan, 2000000, $fiturBadan],
        ];

        ServicePackage::query()->delete();

        foreach ($paket as [$nama, $jenis, $harga, $fitur]) {
            ServicePackage::query()->create([
                'nama_paket' => $nama,
                'jenis_klien' => $jenis,
                'harga' => $harga,
                'fitur' => $fitur,
            ]);
        }
    }
}
