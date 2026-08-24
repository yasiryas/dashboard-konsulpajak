<?php

use App\Enums\StatusLaporan;
use App\Models\ClientProfile;
use App\Models\Document;
use App\Models\TaxReport;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\UploadedFile;

function clientProfileFor(User $user): ClientProfile
{
    return ClientProfile::factory()->create([
        'user_id' => $user->id,
        'drive_folder_id' => 'folder-123',
    ]);
}

function taxReportFor(ClientProfile $profile, array $attributes = []): TaxReport
{
    return TaxReport::factory()->create([
        'client_id' => $profile->id,
        ...$attributes,
    ]);
}

test('guests are redirected from client tax report pages', function () {
    $report = TaxReport::factory()->create();

    $this->get(route('laporan.show', ['current_team' => $report->client->user->currentTeam->slug, 'taxReport' => $report]))
        ->assertRedirect(route('login'));
});

test('klien can view their own tax report', function () {
    $user = User::factory()->create();
    $profile = clientProfileFor($user);
    $report = taxReportFor($profile);

    $response = $this->actingAs($user)
        ->get(route('laporan.show', ['current_team' => $user->currentTeam->slug, 'taxReport' => $report]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('laporan/Show')
        ->where('laporan.id', $report->id)
        ->where('laporan.periode', $report->periode));
});

test('klien cannot view another client tax report', function () {
    $user = User::factory()->create();
    $otherReport = taxReportFor(clientProfileFor(User::factory()->create()));

    $this->actingAs($user)
        ->get(route('laporan.show', ['current_team' => $user->currentTeam->slug, 'taxReport' => $otherReport]))
        ->assertNotFound();
});

test('admin is forbidden from client-only report pages', function () {
    $admin = User::factory()->create(['role' => \App\Enums\UserRole::Admin]);
    $report = taxReportFor(clientProfileFor(User::factory()->create()));

    $this->actingAs($admin)
        ->get(route('laporan.show', ['current_team' => $admin->currentTeam->slug, 'taxReport' => $report]))
        ->assertForbidden();
});

test('klien can upload a document and move report to diproses', function () {
    $user = User::factory()->create();
    $profile = clientProfileFor($user);
    $report = taxReportFor($profile, ['status' => StatusLaporan::MenungguDokumen]);

    $this->mock(GoogleDriveService::class, function ($mock) use ($profile) {
        $mock->shouldReceive('uploadFile')
            ->once()
            ->with('folder-123', \Mockery::on(fn ($file) => $file instanceof UploadedFile))
            ->andReturn(['id' => 'drive-file-1', 'url' => 'https://drive.example/file/1']);
    });

    $response = $this->actingAs($user)
        ->post(route('laporan.documents.store', ['current_team' => $user->currentTeam->slug, 'taxReport' => $report]), [
            'jenis_dokumen' => 'bukti_potong',
            'file' => UploadedFile::fake()->create('bukti-potong.pdf', 100, 'application/pdf'),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($report->refresh()->status)->toBe(StatusLaporan::Diproses);
    expect(Document::query()->where('tax_report_id', $report->id)->count())->toBe(1);

    $document = Document::query()->where('tax_report_id', $report->id)->first();
    expect($document->drive_file_id)->toBe('drive-file-1');
    expect($document->uploaded_by)->toBe($user->id);
});

test('upload keeps status when report already diproses', function () {
    $user = User::factory()->create();
    $profile = clientProfileFor($user);
    $report = taxReportFor($profile, ['status' => StatusLaporan::Diproses]);

    $this->mock(GoogleDriveService::class, function ($mock) {
        $mock->shouldReceive('uploadFile')->once()->andReturn(['id' => 'x', 'url' => null]);
    });

    $this->actingAs($user)
        ->post(route('laporan.documents.store', ['current_team' => $user->currentTeam->slug, 'taxReport' => $report]), [
            'jenis_dokumen' => 'invoice',
            'file' => UploadedFile::fake()->create('invoice.pdf', 50, 'application/pdf'),
        ]);

    expect($report->refresh()->status)->toBe(StatusLaporan::Diproses);
});

test('upload fails gracefully without a drive folder', function () {
    $user = User::factory()->create();
    $profile = ClientProfile::factory()->create(['user_id' => $user->id, 'drive_folder_id' => null]);
    $report = taxReportFor($profile, ['status' => StatusLaporan::MenungguDokumen]);

    $response = $this->actingAs($user)
        ->post(route('laporan.documents.store', ['current_team' => $user->currentTeam->slug, 'taxReport' => $report]), [
            'jenis_dokumen' => 'npwp',
            'file' => UploadedFile::fake()->create('npwp.pdf', 10, 'application/pdf'),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect(Document::count())->toBe(0);
    expect($report->refresh()->status)->toBe(StatusLaporan::MenungguDokumen);
});

test('upload rejects unsupported file types', function () {
    $user = User::factory()->create();
    $profile = clientProfileFor($user);
    $report = taxReportFor($profile);

    $response = $this->actingAs($user)
        ->post(route('laporan.documents.store', ['current_team' => $user->currentTeam->slug, 'taxReport' => $report]), [
            'jenis_dokumen' => 'lainnya',
            'file' => UploadedFile::fake()->create('script.exe', 10),
        ]);

    $response->assertSessionHasErrors('file');
    expect(Document::count())->toBe(0);
});
