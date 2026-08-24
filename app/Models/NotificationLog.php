<?php

namespace App\Models;

use App\Enums\NotifikasiChannel;
use App\Enums\NotifikasiStatus;
use App\Enums\NotifikasiTipe;
use Database\Factories\NotificationLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $client_id
 * @property NotifikasiTipe $tipe
 * @property NotifikasiChannel $channel
 * @property NotifikasiStatus $status
 * @property Carbon|null $sent_at
 * @property-read ClientProfile $client
 */
#[Fillable(['client_id', 'tipe', 'channel', 'status', 'sent_at'])]
class NotificationLog extends Model
{
    /** @use HasFactory<NotificationLogFactory> */
    use HasFactory;

    protected $table = 'notifications_log';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipe' => NotifikasiTipe::class,
            'channel' => NotifikasiChannel::class,
            'status' => NotifikasiStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ClientProfile, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class);
    }
}
