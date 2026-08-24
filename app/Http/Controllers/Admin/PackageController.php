<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JenisKlien;
use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Packages/Index', [
            'packages' => ServicePackage::query()
                ->withCount(['clientProfiles as klien_count'])
                ->orderBy('harga')
                ->get()
                ->map(fn (ServicePackage $package) => [
                    'id' => $package->id,
                    'namaPaket' => $package->nama_paket,
                    'deskripsi' => $package->deskripsi,
                    'jenisKlien' => $package->jenis_klien->label(),
                    'harga' => (float) $package->harga,
                    'klienCount' => $package->klien_count,
                ])
                ->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        ServicePackage::create($validated);

        return back()->with('success', "Paket {$validated['nama_paket']} berhasil ditambahkan.");
    }

    public function update(Request $request, string $currentTeam, ServicePackage $package): RedirectResponse
    {
        $validated = $this->validated($request);

        $package->update($validated);

        return back()->with('success', "Paket {$validated['nama_paket']} berhasil diperbarui.");
    }

    public function destroy(string $currentTeam, ServicePackage $package): RedirectResponse
    {
        if ($package->clientProfiles()->exists()) {
            return back()->with('error', 'Paket masih dipakai klien dan tidak bisa dihapus.');
        }

        $package->delete();

        return back()->with('success', "Paket {$package->nama_paket} dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request): array
    {
        /** @var array{nama_paket: string, deskripsi?: ?string, jenis_klien: string, harga: numeric} */
        return $request->validate([
            'nama_paket' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'jenis_klien' => ['required', Rule::enum(JenisKlien::class)],
            'harga' => ['required', 'numeric', 'min:0'],
            'fitur' => ['required', 'array'],
        ]);
    }
}
