<?php

namespace Database\Factories;

use App\Enums\JenisLaporan;
use App\Enums\StatusLaporan;
use App\Models\ClientProfile;
use App\Models\TaxReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxReport>
 */
class TaxReportFactory extends Factory
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
            'jenis_laporan' => JenisLaporan::SptMasa,
            'periode' => now()->format('Y-m'),
            'status' => StatusLaporan::Draft,
            'deadline_tanggal' => now()->addDays(10)->toDateString(),
        ];
    }
}
