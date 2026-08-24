<?php

use App\Console\Commands\SendDeadlineReminders;
use App\Models\TeamInvitation;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    TeamInvitation::query()
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->delete();
})->daily()->description('Delete expired team invitations');

Schedule::command(SendDeadlineReminders::class)
    ->dailyAt('08:00')
    ->description('Kirim reminder WA deadline H-7 dan H-1');
