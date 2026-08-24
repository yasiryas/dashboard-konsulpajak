<?php

namespace App\Enums;

enum NotifikasiTipe: string
{
    case ReminderDeadline = 'reminder_deadline';
    case StatusUpdate = 'status_update';

    public function label(): string
    {
        return match ($this) {
            self::ReminderDeadline => 'Reminder Deadline',
            self::StatusUpdate => 'Update Status',
        };
    }
}
