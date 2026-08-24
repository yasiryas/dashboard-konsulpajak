<?php

namespace App\Http\Controllers;

use App\Models\ClientProfile;
use App\Models\TeamInvitation;
use App\Models\TaxReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'pendingInvitations' => $this->pendingInvitations($request),
            'ringkasan' => $this->ringkasan($request),
            'laporanAktif' => $this->laporanAktif($request),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function pendingInvitations(Request $request): array
    {
        $email = strtolower($request->user()->email);

        return TeamInvitation::query()
            ->with(['inviter', 'team'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn (TeamInvitation $invitation) => [
                'code' => $invitation->code,
                'inviterName' => $invitation->inviter->name,
                'team' => [
                    'name' => $invitation->team->name,
                    'slug' => $invitation->team->slug,
                ],
            ])
            ->all();
    }

    /**
     * @return array<string, int|string|null>
     */
    protected function ringkasan(Request $request): array
    {
        /** @var ClientProfile|null $profile */
        $profile = $request->user()->activeClientProfile();

        if (! $profile) {
            return ['menungguDokumen' => 0, 'diproses' => 0, 'dilaporkan' => 0, 'deadlineTerdekat' => null];
        }

        $statusCounts = $profile->taxReports()
            ->whereNotNull('deadline_tanggal')
            ->selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $nearest = $profile->taxReports()
            ->whereIn('status', ['menunggu_dokumen', 'diproses'])
            ->whereNotNull('deadline_tanggal')
            ->orderBy('deadline_tanggal')
            ->first(['deadline_tanggal']);

        return [
            'menungguDokumen' => $statusCounts['menunggu_dokumen'] ?? 0,
            'diproses' => $statusCounts['diproses'] ?? 0,
            'dilaporkan' => $statusCounts['dilaporkan'] ?? 0,
            'deadlineTerdekat' => $nearest?->deadline_tanggal?->toDateString(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function laporanAktif(Request $request): array
    {
        /** @var ClientProfile|null $profile */
        $profile = $request->user()->activeClientProfile();

        if (! $profile) {
            return [];
        }

        return $profile->taxReports()
            ->where('status', '!=', 'selesai')
            ->withCount('documents')
            ->orderBy('deadline_tanggal')
            ->limit(10)
            ->get()
            ->map(fn (TaxReport $report) => [
                'id' => $report->id,
                'jenisLaporan' => $report->jenis_laporan->label(),
                'periode' => $report->periode,
                'status' => $report->status->value,
                'statusLabel' => $report->status->label(),
                'deadline' => $report->deadline_tanggal?->toDateString(),
                'dokumenCount' => $report->documents_count,
            ])
            ->all();
    }
}
