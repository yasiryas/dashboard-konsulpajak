<?php

use App\Enums\JenisLaporan;
use App\Enums\StatusLaporan;
use App\Enums\UserRole;
use App\Models\ClientProfile;
use App\Models\TaxReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

function adminUser(): User
{
    return User::factory()->create(['role' => UserRole::Admin]);
}

test('guests are redirected from rekap pages', function () {
    $this->get(route('admin.rekap.bulanan', ['current_team' => 'x']))
        ->assertRedirect(route('login'));

    $this->get(route('admin.rekap.tahunan', ['current_team' => 'x']))
        ->assertRedirect(route('login'));
});

test('non admin users cannot visit rekap pages', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('admin.rekap.bulanan', ['current_team' => $team->slug]))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.rekap.tahunan', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('admin can visit monthly recap page with grouped data', function () {
    $admin = adminUser();

    $client = ClientProfile::factory()->create(['nama_entitas' => 'Klinik Sehat']);
    TaxReport::factory()->create([
        'client_id' => $client->id,
        'periode' => '2026-08',
        'status' => StatusLaporan::Dilaporkan,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.rekap.bulanan', ['current_team' => $admin->currentTeam->slug, 'periode' => '2026-08']));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('admin/Rekap/Bulanan')
        ->where('recap.periode', '2026-08')
        ->where('recap.total', 1)
        ->has('recap.sections', 1)
        ->where('recap.sections.0.status', 'dilaporkan')
        ->where('recap.sections.0.rows.0.namaEntitas', 'Klinik Sehat')
    );
});

test('admin can download monthly recap pdf', function () {
    $admin = adminUser();

    TaxReport::factory()->create(['periode' => '2026-08']);

    $response = $this->actingAs($admin)
        ->get(route('admin.rekap.bulanan.pdf', ['current_team' => $admin->currentTeam->slug, 'periode' => '2026-08']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('admin can visit yearly recap page with matrix data', function () {
    $admin = adminUser();

    $client = ClientProfile::factory()->create(['nama_entitas' => 'Klinik Sehat']);
    TaxReport::factory()->create([
        'client_id' => $client->id,
        'jenis_laporan' => JenisLaporan::SptMasa,
        'periode' => '2026-01',
        'status' => StatusLaporan::Selesai,
    ]);
    TaxReport::factory()->create([
        'client_id' => $client->id,
        'jenis_laporan' => JenisLaporan::SptTahunanBadan,
        'periode' => '2026-12',
        'status' => StatusLaporan::Diproses,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.rekap.tahunan', ['current_team' => $admin->currentTeam->slug, 'tahun' => 2026]));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('admin/Rekap/Tahunan')
        ->where('recap.year', 2026)
        ->has('recap.rows', 1)
        ->where('recap.rows.0.cells.0', 'S')
        ->where('recap.rows.0.selesaiCount', 1)
        ->has('recap.annualReports', 1)
        ->where('recap.annualReports.0.jenisLaporan', 'SPT Tahunan Badan')
    );
});

test('admin can download yearly recap pdf', function () {
    $admin = adminUser();

    TaxReport::factory()->create(['periode' => '2026-03']);

    $response = $this->actingAs($admin)
        ->get(route('admin.rekap.tahunan.pdf', ['current_team' => $admin->currentTeam->slug, 'tahun' => 2026]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});
