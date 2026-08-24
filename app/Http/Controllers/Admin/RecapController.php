<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxReport;
use App\Services\RecapService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RecapController extends Controller
{
    public function __construct(private readonly RecapService $recaps) {}

    public function bulanan(Request $request): Response
    {
        $periode = $this->resolvePeriode($request);

        return Inertia::render('admin/Rekap/Bulanan', [
            'recap' => $this->recaps->monthly($periode),
            'years' => $this->availableYears(),
        ]);
    }

    public function bulananPdf(Request $request): SymfonyResponse
    {
        $periode = $this->resolvePeriode($request);

        return Pdf::loadView('admin.rekap.bulanan-pdf', [
            'recap' => $this->recaps->monthly($periode),
        ])
            ->setPaper('a4', 'landscape')
            ->stream("rekap-bulanan-{$periode}.pdf");
    }

    public function tahunan(Request $request): Response
    {
        $year = $this->resolveYear($request);

        return Inertia::render('admin/Rekap/Tahunan', [
            'recap' => $this->recaps->yearly($year),
            'years' => $this->availableYears(),
        ]);
    }

    public function tahunanPdf(Request $request): SymfonyResponse
    {
        $year = $this->resolveYear($request);

        return Pdf::loadView('admin.rekap.tahunan-pdf', [
            'recap' => $this->recaps->yearly($year),
        ])
            ->setPaper('a4', 'landscape')
            ->stream("rekap-tahunan-{$year}.pdf");
    }

    private function resolvePeriode(Request $request): string
    {
        $validated = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m'],
        ]);

        return $validated['periode'] ?? now()->format('Y-m');
    }

    private function resolveYear(Request $request): int
    {
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        return (int) ($validated['tahun'] ?? now()->year);
    }

    /**
     * @return array<int, int>
     */
    private function availableYears(): array
    {
        $years = TaxReport::query()
            ->selectRaw('DISTINCT SUBSTRING(periode, 1, 4) AS tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn (string $year) => (int) $year);

        return $years->push(now()->year)->unique()->sortDesc()->values()->all();
    }
}
