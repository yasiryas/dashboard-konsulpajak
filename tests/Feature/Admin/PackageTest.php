<?php

use App\Enums\UserRole;
use App\Models\ServicePackage;
use App\Models\User;

function packageAdminUser(): User
{
    return User::factory()->create(['role' => UserRole::Admin]);
}

test('guests are redirected from package pages', function () {
    $admin = packageAdminUser();

    $this->get(route('admin.packages.index', ['current_team' => $admin->currentTeam->slug]))
        ->assertRedirect(route('login'));
});

test('klien cannot manage packages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.packages.index', ['current_team' => $user->currentTeam->slug]))
        ->assertForbidden();
});

test('admin can view package index', function () {
    $admin = packageAdminUser();
    ServicePackage::factory()->count(3)->create();

    $response = $this->actingAs($admin)
        ->get(route('admin.packages.index', ['current_team' => $admin->currentTeam->slug]));

    $response->assertOk();
});

test('admin can create a package', function () {
    $admin = packageAdminUser();

    $response = $this->actingAs($admin)
        ->post(route('admin.packages.store', ['current_team' => $admin->currentTeam->slug]), [
            'nama_paket' => 'Paket Uji Coba',
            'deskripsi' => 'Deskripsi paket',
            'jenis_klien' => 'umkm',
            'harga' => 750000,
            'fitur' => ['konsultasi_bulanan' => 2],
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('service_packages', [
        'nama_paket' => 'Paket Uji Coba',
        'harga' => 750000,
    ]);
});

test('store validates required fields', function () {
    $admin = packageAdminUser();

    $response = $this->actingAs($admin)
        ->post(route('admin.packages.store', ['current_team' => $admin->currentTeam->slug]), []);

    $response->assertSessionHasErrors(['nama_paket', 'jenis_klien', 'harga']);
});

test('admin can update a package', function () {
    $admin = packageAdminUser();
    $package = ServicePackage::factory()->create(['harga' => 500000]);

    $this->actingAs($admin)
        ->put(route('admin.packages.update', ['current_team' => $admin->currentTeam->slug, 'package' => $package]), [
            'nama_paket' => $package->nama_paket,
            'deskripsi' => $package->deskripsi,
            'jenis_klien' => $package->jenis_klien->value,
            'harga' => 900000,
            'fitur' => $package->fitur,
        ])
        ->assertRedirect();

    expect($package->refresh()->harga)->toBe('900000.00');
});

test('admin can delete an unused package', function () {
    $admin = packageAdminUser();
    $package = ServicePackage::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.packages.destroy', ['current_team' => $admin->currentTeam->slug, 'package' => $package]))
        ->assertRedirect();

    expect(ServicePackage::query()->whereKey($package->id)->count())->toBe(0);
});

test('delete is blocked when the package is used by clients', function () {
    $admin = packageAdminUser();
    $package = ServicePackage::factory()->create();
    \App\Models\ClientProfile::factory()->create(['package_id' => $package->id]);

    $response = $this->actingAs($admin)
        ->delete(route('admin.packages.destroy', ['current_team' => $admin->currentTeam->slug, 'package' => $package]));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(ServicePackage::query()->whereKey($package->id)->exists())->toBeTrue();
});
