<?php

namespace App\Models;

use App\Enums\JenisLaporan;
use App\Enums\StatusLaporan;
use Database\Factories\TaxReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $client_id
 * @property JenisLaporan $jenis_laporan
 * @property string $periode
 * @property StatusLaporan $status
 * @property Carbon|null $deadline_tanggal
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read ClientProfile $client
 * @property-read Collection<int, Document> $documents
 */
#[Fillable(['client_id', 'jenis_laporan', 'periode', 'status', 'deadline_tanggal'])]
class TaxReport extends Model
{
    /** @use HasFactory<TaxReportFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis_laporan' => JenisLaporan::class,
            'status' => StatusLaporan::class,
            'deadline_tanggal' => 'date',
        ];
    }

    /**
     * @return BelongsTo<ClientProfile, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class);
    }

    /**
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Laporan dengan deadline dalam N hari ke depan.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    #[Scope]
    protected function deadlineInNextDays(Builder $query, int $days): Builder
    {
        $today = Carbon::today();
        $horizon = Carbon::today()->addDays($days);

        return $query
            ->whereBetween('deadline_tanggal', [$today, $horizon])
            ->orderBy('deadline_tanggal');
    }
}
