<?php

namespace Database\Factories;

use App\Enums\JenisKlien;
use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServicePackage>
 */
class ServicePackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_paket' => fake()->words(4, true),
            'jenis_klien' => JenisKlien::Umkm,
            'harga' => fake()->numberBetween(750000, 5000000),
            'fitur' => ['konsultasi_wa' => true],
        ];
    }
}
