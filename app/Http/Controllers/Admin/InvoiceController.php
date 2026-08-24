<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusBayar;
use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->string('status')->toString();

        $query = Invoice::query()
            ->with('clientProfile:id,nama_entitas');

        if ($status !== '') {
            $query->where('status_bayar', $status);
        }

        $invoices = $query->orderByDesc('periode')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Invoices/Index', [
            'invoices' => [
                'data' => $invoices->through(fn (Invoice $invoice) => [
                    'id' => $invoice->id,
                    'klien' => $invoice->clientProfile?->nama_entitas,
                    'periode' => $this->periodeLabel($invoice->periode),
                    'nominal' => (float) $invoice->nominal,
                    'status' => $invoice->status_bayar->value,
                    'statusLabel' => $invoice->status_bayar->label(),
                ])->all(),
                'meta' => [
                    'currentPage' => $invoices->currentPage(),
                    'lastPage' => $invoices->lastPage(),
                    'total' => $invoices->total(),
                ],
            ],
            'ringkasan' => [
                'totalTagihan' => (float) Invoice::whereNot('status_bayar', StatusBayar::Batal->value)->sum('nominal'),
                'lunas' => (float) Invoice::where('status_bayar', StatusBayar::Lunas->value)->sum('nominal'),
                'outstanding' => (float) Invoice::where('status_bayar', StatusBayar::BelumDibayar->value)->sum('nominal'),
            ],
            'filters' => ['status' => $status],
            'clients' => ClientProfile::query()
                ->orderBy('nama_entitas')
                ->get(['id', 'nama_entitas'])
                ->map(fn (ClientProfile $client) => [
                    'id' => $client->id,
                    'namaEntitas' => $client->nama_entitas,
                ])
                ->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_profile_id' => ['required', Rule::exists('client_profiles', 'id')],
            'periode' => ['required', 'date_format:Y-m'],
            'nominal' => ['required', 'numeric', 'min:0'],
        ]);

        Invoice::create($validated);

        return back()->with('success', 'Tagihan berhasil dibuat.');
    }

    public function updateStatus(Request $request, string $currentTeam, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'status_bayar' => ['required', Rule::enum(StatusBayar::class)],
        ]);

        $invoice->update($validated);

        $label = StatusBayar::from($validated['status_bayar'])->label();

        return back()->with('success', "Tagihan ditandai {$label}.");
    }

    public function destroy(string $currentTeam, Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return back()->with('success', 'Tagihan dihapus.');
    }

    private function periodeLabel(string $periode): string
    {
        return Carbon::createFromFormat('Y-m', $periode)?->locale('id')->translatedFormat('F Y') ?? $periode;
    }
}
