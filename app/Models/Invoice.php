<?php

namespace App\Models;

use App\Enums\StatusBayar;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $client_profile_id
 * @property string $periode
 * @property string $nominal
 * @property StatusBayar $status_bayar
 * @property-read ClientProfile $clientProfile
 */
#[Fillable(['client_profile_id', 'periode', 'nominal', 'status_bayar'])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_bayar' => StatusBayar::class,
        ];
    }

    /**
     * @return BelongsTo<ClientProfile, $this>
     */
    public function clientProfile(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class);
    }
}
