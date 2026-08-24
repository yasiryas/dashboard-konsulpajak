<?php

namespace App\Models;

use App\Enums\JenisKlien;
use Database\Factories\ServicePackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nama_paket
 * @property JenisKlien $jenis_klien
 * @property string $harga
 * @property array<string, int|bool> $fitur
 * @property-read Collection<int, ClientProfile> $clientProfiles
 */
#[Fillable(['nama_paket', 'jenis_klien', 'harga', 'fitur'])]
class ServicePackage extends Model
{
    /** @use HasFactory<ServicePackageFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis_klien' => JenisKlien::class,
            'harga' => 'decimal:2',
            'fitur' => 'array',
        ];
    }

    /**
     * @return HasMany<ClientProfile, $this>
     */
    public function clientProfiles(): HasMany
    {
        return $this->hasMany(ClientProfile::class, 'package_id');
    }
}
