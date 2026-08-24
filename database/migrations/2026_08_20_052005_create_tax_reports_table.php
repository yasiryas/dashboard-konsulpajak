<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tax_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('client_profiles')->cascadeOnDelete();
            $table->string('jenis_laporan')->index();
            $table->string('periode', 7)->comment('Format: YYYY-MM, contoh 2026-08');
            $table->string('status')->default('draft')->index();
            $table->date('deadline_tanggal')->nullable()->index();
            $table->timestamps();

            $table->unique(['client_id', 'jenis_laporan', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_reports');
    }
};
