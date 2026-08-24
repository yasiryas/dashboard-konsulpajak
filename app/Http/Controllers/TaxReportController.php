<?php

namespace App\Http\Controllers;

use App\Enums\JenisDokumen;
use App\Enums\StatusLaporan;
use App\Models\ClientProfile;
use App\Models\Document;
use App\Models\TaxReport;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TaxReportController extends Controller
{
    public function show(Request $request, string $currentTeam, TaxReport $taxReport): Response
    {
        $this->authorizeClient($request, $taxReport);

        $taxReport->load(['documents.uploader:id,name']);

        return Inertia::render('laporan/Show', [
            'laporan' => [
                'id' => $taxReport->id,
                'jenisLaporan' => $taxReport->jenis_laporan->label(),
                'periode' => $taxReport->periode,
                'status' => $taxReport->status->value,
                'statusLabel' => $taxReport->status->label(),
                'deadline' => $taxReport->deadline_tanggal?->toDateString(),
                'documents' => $taxReport->documents->map(fn (Document $document) => [
                    'id' => $document->id,
                    'jenisDokumen' => $document->jenis_dokumen->label(),
                    'namaFile' => $document->nama_file,
                    'driveFileUrl' => $document->drive_file_url,
                    'uploadedBy' => $document->uploader?->name,
                    'uploadedAt' => $document->created_at->format('d M Y H:i'),
                ])->all(),
            ],
        ]);
    }

    public function upload(Request $request, string $currentTeam, TaxReport $taxReport): RedirectResponse
    {
        $this->authorizeClient($request, $taxReport);

        /** @var ClientProfile|null $profile */
        $profile = $request->user()->activeClientProfile();

        if (! $profile?->drive_folder_id) {
            return back()->with('error', 'Folder Google Drive belum tersedia untuk akun ini. Hubungi konsultan Anda.');
        }

        $validated = $request->validate([
            'jenis_dokumen' => ['required', Rule::enum(JenisDokumen::class)],
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,xls,xlsx,doc,docx'],
        ]);

        $uploaded = app(GoogleDriveService::class)->uploadFile($profile->drive_folder_id, $request->file('file'));

        Document::create([
            'tax_report_id' => $taxReport->id,
            'jenis_dokumen' => JenisDokumen::from($validated['jenis_dokumen']),
            'nama_file' => $request->file('file')->getClientOriginalName(),
            'drive_file_id' => $uploaded['id'],
            'drive_file_url' => $uploaded['url'],
            'uploaded_by' => $request->user()->id,
        ]);

        if ($taxReport->status === StatusLaporan::MenungguDokumen) {
            $taxReport->update(['status' => StatusLaporan::Diproses]);
        }

        return back()->with('success', "Dokumen {$validated['jenis_dokumen']} berhasil diupload.");
    }

    protected function authorizeClient(Request $request, TaxReport $taxReport): void
    {
        /** @var ClientProfile|null $profile */
        $profile = $request->user()->activeClientProfile();

        abort_if(! $profile || $taxReport->client_id !== $profile->id, 404);
    }
}
