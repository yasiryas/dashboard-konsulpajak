<?php

namespace App\Http\Controllers;

use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfilController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('profil/Index', [
            'profil' => $this->profilPayload($request),
        ]);
    }

    public function update(Request $request): RedirectResponse|Response
    {
        $validated = $request->validate([
            'nama_entitas' => ['required', 'string', 'max:255'],
        ]);

        /** @var ClientProfile|null $profile */
        $profile = $request->user()->activeClientProfile();

        abort_if(! $profile, 404);

        $profile->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function profilPayload(Request $request): array
    {
        /** @var ClientProfile|null $profile */
        $profile = $request->user()->activeClientProfile()?->load('package:id,nama_paket,harga');

        return [
            'namaEntitas' => $profile?->nama_entitas,
            'jenisKlien' => $profile?->jenis_klien->label(),
            'npwp' => $profile?->npwp,
            'paket' => $profile?->package ? [
                'nama' => $profile->package->nama_paket,
                'harga' => number_format((float) $profile->package->harga, 0, ',', '.'),
            ] : null,
        ];
    }
}
