<?php

namespace App\Http\Controllers;

use App\Models\ClientProfile;
use App\Models\TaxReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RiwayatController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var ClientProfile|null $profile */
        $profile = $request->user()->activeClientProfile();

        $reports = $profile
            ? $profile->taxReports()
                ->where('status', 'selesai')
                ->orderByDesc('periode')
                ->get(['id', 'jenis_laporan', 'periode', 'deadline_tanggal', 'status'])
            : collect();

        $groups = [];
        foreach ($reports->groupBy(fn (TaxReport $report) => substr($report->periode, 0, 4)) as $tahun => $items) {
            $groups[] = [
                'tahun' => (int) $tahun,
                'items' => $items->map(fn (TaxReport $report) => [
                    'id' => $report->id,
                    'jenisLaporan' => $report->jenis_laporan->label(),
                    'periode' => $report->periode,
                    'deadline' => $report->deadline_tanggal?->toDateString(),
                    'status' => $report->status->value,
                    'statusLabel' => $report->status->label(),
                ])->values()->all(),
            ];
        }

        usort($groups, fn (array $a, array $b) => $b['tahun'] <=> $a['tahun']);

        return Inertia::render('riwayat/Index', [
            'groups' => $groups,
        ]);
    }
}
