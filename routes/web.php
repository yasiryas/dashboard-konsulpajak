<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\NotificationLogController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\RealtimeController;
use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Admin\TaxReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\TaxReportController as ClientTaxReportController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, 'role:klien'])
    ->group(function () {
        Route::get('laporan/{taxReport}', [ClientTaxReportController::class, 'show'])->name('laporan.show');
        Route::post('laporan/{taxReport}/dokumen', [ClientTaxReportController::class, 'upload'])->name('laporan.documents.store');
        Route::get('riwayat', RiwayatController::class)->name('riwayat.index');
        Route::get('profil', [ProfilController::class, 'show'])->name('profil.show');
        Route::put('profil', [ProfilController::class, 'update'])->name('profil.update');
    });

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->group(function () {
        Route::get('admin/dashboard', AdminDashboardController::class)->name('admin.dashboard');
        Route::get('admin/realtime', RealtimeController::class)->name('admin.realtime');
        Route::get('admin/rekap/bulanan', [RecapController::class, 'bulanan'])->name('admin.rekap.bulanan');
        Route::get('admin/rekap/bulanan/pdf', [RecapController::class, 'bulananPdf'])->name('admin.rekap.bulanan.pdf');
        Route::get('admin/rekap/tahunan', [RecapController::class, 'tahunan'])->name('admin.rekap.tahunan');
        Route::get('admin/rekap/tahunan/pdf', [RecapController::class, 'tahunanPdf'])->name('admin.rekap.tahunan.pdf');
        Route::get('admin/klien', [ClientController::class, 'index'])->name('admin.clients.index');
        Route::post('admin/klien', [ClientController::class, 'store'])->name('admin.clients.store');
        Route::get('admin/klien/{client}', [ClientController::class, 'show'])->name('admin.clients.show');
        Route::put('admin/laporan/{taxReport}/status', [TaxReportController::class, 'updateStatus'])
            ->name('admin.taxReports.status');
        Route::get('admin/paket-layanan', [PackageController::class, 'index'])->name('admin.packages.index');
        Route::post('admin/paket-layanan', [PackageController::class, 'store'])->name('admin.packages.store');
        Route::put('admin/paket-layanan/{package}', [PackageController::class, 'update'])->name('admin.packages.update');
        Route::delete('admin/paket-layanan/{package}', [PackageController::class, 'destroy'])->name('admin.packages.destroy');
        Route::get('admin/notifikasi', [NotificationLogController::class, 'index'])->name('admin.notifications.index');
        Route::get('admin/invoice', [InvoiceController::class, 'index'])->name('admin.invoices.index');
        Route::post('admin/invoice', [InvoiceController::class, 'store'])->name('admin.invoices.store');
        Route::put('admin/invoice/{invoice}/status', [InvoiceController::class, 'updateStatus'])
            ->name('admin.invoices.status');
        Route::delete('admin/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('admin.invoices.destroy');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
