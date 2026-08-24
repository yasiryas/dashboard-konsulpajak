<?php

namespace Database\Factories;

use App\Enums\JenisKlien;
use App\Models\ClientProfile;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientProfile>
 */
class ClientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_entitas' => fake()->company(),
            'jenis_klien' => JenisKlien::Umkm,
            'npwp' => fake()->numerify('###############'),
            'package_id' => ServicePackage::factory(),
            'drive_folder_id' => null,
        ];
    }
}
