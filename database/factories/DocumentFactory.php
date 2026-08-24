<?php

namespace Database\Factories;

use App\Enums\JenisDokumen;
use App\Models\Document;
use App\Models\TaxReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tax_report_id' => TaxReport::factory(),
            'jenis_dokumen' => JenisDokumen::BuktiPotong,
            'nama_file' => fake()->word().'.pdf',
            'drive_file_id' => null,
            'drive_file_url' => null,
            'uploaded_by' => null,
        ];
    }
}
