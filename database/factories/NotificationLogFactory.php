<?php

namespace Database\Factories;

use App\Enums\NotifikasiChannel;
use App\Enums\NotifikasiStatus;
use App\Enums\NotifikasiTipe;
use App\Models\ClientProfile;
use App\Models\NotificationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationLog>
 */
class NotificationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => ClientProfile::factory(),
            'tipe' => NotifikasiTipe::StatusUpdate,
            'channel' => NotifikasiChannel::Whatsapp,
            'status' => NotifikasiStatus::Pending,
            'sent_at' => null,
        ];
    }
}
