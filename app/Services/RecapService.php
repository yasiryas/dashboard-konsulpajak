<?php

namespace App\Services;

use App\Enums\JenisLaporan;
use App\Enums\StatusLaporan;
use App\Models\ClientProfile;
use App\Models\TaxReport;
use Illuminate\Support\Carbon;

class RecapService
{
    /**
     * Urutan tampil status pada rekap: yang sudah berjalan di atas.
     *
     * @var list<StatusLaporan>
     */
    private const STATUS_URUTAN = [
        StatusLaporan::Dilaporkan,
        StatusLaporan::Selesai,
        StatusLaporan::Diproses,
        StatusLaporan::MenungguDokumen,
        StatusLaporan::Draft,
    ];

    private const NAMA_BULAN = [
        'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN',
        'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES',
    ];

    /**
     * Kode satu huruf untuk sel matriks tahunan.
     */
    public static function statusCode(StatusLaporan $status): string
    {
        return match ($status) {
            StatusLaporan::Selesai => 'S',
            StatusLaporan::Dilaporkan => 'L',
            StatusLaporan::Diproses => 'P',
            StatusLaporan::MenungguDokumen => 'M',
            StatusLaporan::Draft => 'D',
        };
    }

    /**
     * Rekap laporan pajak satu bulan, dikelompokkan per status.
     *
     * @return array<string, mixed>
     */
    public function monthly(string $periode): array
    {
        $reports = TaxReport::query()
            ->where('periode', $periode)
            ->with('client:id,nama_entitas,jenis_klien')
            ->get()
            ->sortBy([
                fn (TaxReport $report) => $report->client->nama_entitas,
                fn (TaxReport $report) => $report->jenis_laporan->value,
            ])
            ->values();

        $sections = [];
        $total = 0;

        foreach (self::STATUS_URUTAN as $status) {
            $rows = [];

            foreach ($reports->where('status', $status) as $report) {
                $rows[] = [
                    'no' => count($rows) + 1,
                    'namaEntitas' => $report->client->nama_entitas,
                    'jenisKlien' => $report->client->jenis_klien->label(),
                    'jenisLaporan' => $report->jenis_laporan->label(),
                    'deadline' => $report->deadline_tanggal?->format('d/m/Y'),
                ];
            }

            if ($rows === []) {
                continue;
            }

            $sections[] = [
                'status' => $status->value,
                'label' => $status->label(),
                'count' => count($rows),
                'rows' => $rows,
            ];
            $total += count($rows);
        }

        return [
            'periode' => $periode,
            'periodeLabel' => Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y'),
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'sections' => $sections,
            'total' => $total,
        ];
    }

    /**
     * Rekap setahun: matriks SPT Masa 12 bulan per klien + daftar SPT Tahunan.
     *
     * @return array<string, mixed>
     */
    public function yearly(int $year): array
    {
        $reports = TaxReport::query()
            ->where('periode', 'like', "{$year}-%")
            ->with('client:id,nama_entitas,jenis_klien')
            ->get();

        $clients = ClientProfile::query()
            ->orderBy('nama_entitas')
            ->get(['id', 'nama_entitas']);

        $masaReports = $reports->where('jenis_laporan', JenisLaporan::SptMasa)->groupBy('client_id');

        $rows = [];
        foreach ($clients as $index => $client) {
            $cells = array_fill(0, 12, null);

            foreach ($masaReports->get($client->id, collect()) as $report) {
                $monthIndex = (int) substr($report->periode, 5, 2) - 1;
                $cells[$monthIndex] = self::statusCode($report->status);
            }

            $rows[] = [
                'no' => $index + 1,
                'namaEntitas' => $client->nama_entitas,
                'cells' => $cells,
                'selesaiCount' => count(array_filter(
                    $cells,
                    fn (?string $code) => in_array($code, ['S', 'L'], true),
                )),
            ];
        }

        $monthlyReported = [];
        for ($month = 1; $month <= 12; $month++) {
            $periode = sprintf('%d-%02d', $year, $month);

            $monthlyReported[] = $masaReports
                ->flatten()
                ->where('periode', $periode)
                ->whereIn('status', [StatusLaporan::Dilaporkan, StatusLaporan::Selesai])
                ->count();
        }

        $annualRows = [];
        foreach ($reports->whereIn('jenis_laporan', [
            JenisLaporan::SptTahunanPribadi,
            JenisLaporan::SptTahunanBadan,
        ])->sortBy(fn (TaxReport $report) => $report->client->nama_entitas)->values() as $report) {
            $annualRows[] = [
                'no' => count($annualRows) + 1,
                'namaEntitas' => $report->client->nama_entitas,
                'jenisLaporan' => $report->jenis_laporan->label(),
                'status' => $report->status->value,
                'statusLabel' => $report->status->label(),
                'deadline' => $report->deadline_tanggal?->format('d/m/Y'),
            ];
        }

        return [
            'year' => $year,
            'months' => self::NAMA_BULAN,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'rows' => $rows,
            'monthlyReported' => $monthlyReported,
            'totalMasa' => $masaReports->flatten()->count(),
            'annualReports' => $annualRows,
        ];
    }
}
