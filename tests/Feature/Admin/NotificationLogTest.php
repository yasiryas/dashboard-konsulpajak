<?php

use App\Enums\NotifikasiStatus;
use App\Enums\UserRole;
use App\Models\ClientProfile;
use App\Models\NotificationLog;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('klien cannot view notification logs', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.notifications.index', ['current_team' => $user->currentTeam->slug]))
        ->assertForbidden();
});

test('admin can view notification logs', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $log = NotificationLog::factory()->create();

    $response = $this->actingAs($admin)
        ->get(route('admin.notifications.index', ['current_team' => $admin->currentTeam->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('admin/Notifications/Index')
        ->has('logs.data', 1)
        ->where('logs.data.0.id', $log->id)
        ->where('logs.data.0.klien', $log->client->nama_entitas));
});

test('status filter only returns matching logs', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    NotificationLog::factory()->count(2)->create(['status' => NotifikasiStatus::Terkirim]);
    NotificationLog::factory()->create(['status' => NotifikasiStatus::Gagal]);

    $response = $this->actingAs($admin)
        ->get(route('admin.notifications.index', [
            'current_team' => $admin->currentTeam->slug,
            'status' => 'gagal',
        ]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('admin/Notifications/Index')
        ->has('logs.data', 1)
        ->where('logs.data.0.status', 'gagal'));
});
