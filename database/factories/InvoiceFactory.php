<?php

namespace Database\Factories;

use App\Enums\StatusBayar;
use App\Models\ClientProfile;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_profile_id' => ClientProfile::factory(),
            'periode' => now()->format('Y-m'),
            'nominal' => fake()->randomFloat(2, 500000, 5000000),
            'status_bayar' => StatusBayar::BelumDibayar,
        ];
    }
}
