<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Models\ClientProfile;
use App\Models\Team;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ClientProfile::query()
            ->with(['user:id,name,email', 'package:id,nama_paket'])
            ->withExists(['taxReports as laporan_aktif_count' => fn ($q) => $q->where('status', '!=', 'selesai')]);

        if ($search = trim((string) $request->string('cari'))) {
            $query->where(fn ($q) => $q
                ->where('nama_entitas', 'like', "%{$search}%")
                ->orWhere('npwp', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")));
        }

        if ($jenis = $request->string('jenis')->toString()) {
            $query->where('jenis_klien', $jenis);
        }

        if ($paket = $request->integer('paket')) {
            $query->where('package_id', $paket);
        }

        $clients = $query->orderBy('nama_entitas')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/Clients/Index', [
            'clients' => [
                'data' => $clients->through(fn (ClientProfile $client) => [
                    'id' => $client->id,
                    'namaEntitas' => $client->nama_entitas,
                    'jenisKlien' => $client->jenis_klien->label(),
                    'npwp' => $client->npwp,
                    'email' => $client->user->email,
                    'paket' => $client->package?->nama_paket,
                    'laporanAktifCount' => $client->laporan_aktif_count,
                ])->all(),
                'meta' => [
                    'currentPage' => $clients->currentPage(),
                    'lastPage' => $clients->lastPage(),
                    'total' => $clients->total(),
                ],
            ],
            'filters' => [
                'cari' => $search,
                'jenis' => $request->string('jenis')->toString(),
                'paket' => $paket ?: null,
            ],
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => UserRole::Client,
        ]);

        $team = Team::create([
            'name' => "Tim {$user->name}",
            'is_personal' => true,
        ]);

        $team->members()->attach($user, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $clientProfile = ClientProfile::create([
            'user_id' => $user->id,
            'nama_entitas' => $validated['nama_entitas'],
            'jenis_klien' => $validated['jenis_klien'],
            'npwp' => $validated['npwp'] ?? null,
            'package_id' => $validated['package_id'] ?? null,
        ]);

        try {
            $clientProfile->update([
                'drive_folder_id' => app(GoogleDriveService::class)->createClientFolder(
                    $validated['nama_entitas'],
                ),
            ]);
        } catch (Throwable) {
            return back()->with(
                'warning',
                "Klien dibuat, tapi folder Google Drive gagal dibuat (cek GOOGLE_DRIVE_CREDENTIALS).",
            );
        }

        return back()->with('success', "Klien {$validated['nama_entitas']} berhasil ditambahkan.");
    }

    public function show(Request $request, string $currentTeam, ClientProfile $client): Response
    {
        $client->load(['user:id,name,email', 'package:id,nama_paket', 'taxReports.documents']);

        $taxReports = [];
        foreach ($client->taxReports->sortByDesc('periode') as $report) {
            $documents = [];
            foreach ($report->documents as $document) {
                $documents[] = [
                    'id' => $document->id,
                    'jenisDokumen' => $document->jenis_dokumen->label(),
                    'namaFile' => $document->nama_file,
                    'driveFileUrl' => $document->drive_file_url,
                    'uploadedAt' => $document->updated_at->toIso8601String(),
                ];
            }

            $taxReports[] = [
                'id' => $report->id,
                'jenisLaporan' => $report->jenis_laporan->value,
                'jenisLaporanLabel' => $report->jenis_laporan->label(),
                'periode' => $report->periode,
                'status' => $report->status->value,
                'statusLabel' => $report->status->label(),
                'deadline' => $report->deadline_tanggal?->toDateString(),
                'documents' => $documents,
            ];
        }

        return Inertia::render('admin/Clients/Show', [
            'client' => [
                'id' => $client->id,
                'namaEntitas' => $client->nama_entitas,
                'jenisKlien' => $client->jenis_klien->label(),
                'npwp' => $client->npwp,
                'email' => $client->user->email,
                'paket' => $client->package ? [
                    'nama' => $client->package->nama_paket,
                ] : null,
                'driveFolderId' => $client->drive_folder_id,
                'taxReports' => $taxReports,
            ],
        ]);
    }
}
