<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\ServicePackage;
use App\Models\TaxReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $packages = [];
        foreach (ServicePackage::query()->orderBy('jenis_klien')->get(['id', 'nama_paket', 'jenis_klien']) as $package) {
            $packages[] = [
                'id' => $package->id,
                'nama_paket' => $package->nama_paket,
                'jenis_klien' => $package->jenis_klien->value,
                'jenis_klien_label' => $package->jenis_klien->label(),
            ];
        }

        return Inertia::render('admin/Dashboard', [
            'stats' => Inertia::defer(fn () => $this->adminStats()),
            'clients' => Inertia::defer(fn () => $this->adminClients()),
            'deadlines' => Inertia::defer(fn () => $this->adminDeadlines()),
            'packages' => $packages,
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function adminStats(): array
    {
        return [
            'totalKlien' => ClientProfile::count(),
            'laporanBerjalan' => TaxReport::whereIn('status', [
                StatusLaporan::MenungguDokumen->value,
                StatusLaporan::Diproses->value,
            ])->count(),
            'butuhDokumen' => TaxReport::where('status', StatusLaporan::MenungguDokumen->value)->count(),
            'deadlineTerdekat' => TaxReport::deadlineInNextDays(7)->count(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function adminClients(): array
    {
        $clients = ClientProfile::query()
            ->with([
                'user:id,name,email',
                'package:id,nama_paket',
                'taxReports' => fn ($query) => $query->latest('periode'),
            ])
            ->get()
            ->sortBy('nama_entitas')
            ->values();

        $result = [];
        foreach ($clients as $client) {
            $latest = $client->taxReports->first();

            $result[] = [
                'id' => $client->id,
                'namaEntitas' => $client->nama_entitas,
                'jenisKlien' => $client->jenis_klien->label(),
                'npwp' => $client->npwp,
                'email' => $client->user->email,
                'paket' => $client->package?->nama_paket,
                'laporanTerakhir' => $latest ? [
                    'jenisLaporan' => $latest->jenis_laporan->label(),
                    'periode' => $latest->periode,
                    'status' => $latest->status->value,
                    'statusLabel' => $latest->status->label(),
                    'deadline' => $latest->deadline_tanggal?->toDateString(),
                ] : null,
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function adminDeadlines(): array
    {
        $reports = TaxReport::deadlineInNextDays(7)
            ->with(['client:id,nama_entitas,jenis_klien'])
            ->get();

        $result = [];
        foreach ($reports as $report) {
            $result[] = [
                'id' => $report->id,
                'jenisLaporan' => $report->jenis_laporan->label(),
                'periode' => $report->periode,
                'status' => $report->status->value,
                'statusLabel' => $report->status->label(),
                'deadline' => $report->deadline_tanggal?->toDateString(),
                'client' => [
                    'namaEntitas' => $report->client->nama_entitas,
                    'jenisKlien' => $report->client->jenis_klien->label(),
                ],
            ];
        }

        return $result;
    }
}
