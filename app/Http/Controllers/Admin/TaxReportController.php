<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotifikasiStatus;
use App\Enums\NotifikasiTipe;
use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\TaxReport;
use App\Services\WhatsappNotifierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxReportController extends Controller
{
    public function updateStatus(Request $request, string $currentTeam, TaxReport $taxReport): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(StatusLaporan::class)],
        ]);

        $newStatus = StatusLaporan::from($validated['status']);

        $taxReport->update(['status' => $newStatus]);

        $this->notifyClient($taxReport, $newStatus);

        return back()->with('success', "Status laporan {$taxReport->periode} diperbarui ke {$newStatus->label()}.");
    }

    protected function notifyClient(TaxReport $taxReport, StatusLaporan $status): void
    {
        $taxReport->loadMissing('client.user');
        $phone = $taxReport->client->user->phone ?? null;
        $message = "Halo {$taxReport->client->nama_entitas}, status laporan {$taxReport->jenis_laporan->label()} periode {$taxReport->periode} kini: {$status->label()}.";

        if (blank($phone)) {
            NotificationLog::create([
                'client_id' => $taxReport->client_id,
                'tipe' => NotifikasiTipe::StatusUpdate,
                'channel' => NotifikasiChannel::Whatsapp,
                'status' => NotifikasiStatus::Pending,
            ]);

            return;
        }

        app(WhatsappNotifierService::class)->send(
            $phone,
            $message,
            NotifikasiTipe::StatusUpdate->value,
            $taxReport->client_id,
        );
    }
}
