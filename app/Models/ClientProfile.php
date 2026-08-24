<?php

namespace App\Models;

use App\Enums\JenisKlien;
use Database\Factories\ClientProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $nama_entitas
 * @property JenisKlien $jenis_klien
 * @property string|null $npwp
 * @property int|null $package_id
 * @property string|null $drive_folder_id
 * @property-read User $user
 * @property-read ServicePackage|null $package
 * @property-read Collection<int, TaxReport> $taxReports
 * @property-read Collection<int, NotificationLog> $notificationLogs
 * @property-read Collection<int, Invoice> $invoices
 */
#[Fillable(['user_id', 'nama_entitas', 'jenis_klien', 'npwp', 'package_id', 'drive_folder_id'])]
class ClientProfile extends Model
{
    /** @use HasFactory<ClientProfileFactory> */
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
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<ServicePackage, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }

    /**
     * @return HasMany<TaxReport, $this>
     */
    public function taxReports(): HasMany
    {
        return $this->hasMany(TaxReport::class);
    }

    /**
     * @return HasMany<NotificationLog, $this>
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * @return HasMany<Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
