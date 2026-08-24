<?php

namespace App\Models;

use App\Enums\JenisDokumen;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $tax_report_id
 * @property JenisDokumen $jenis_dokumen
 * @property string $nama_file
 * @property string|null $drive_file_id
 * @property string|null $drive_file_url
 * @property int $uploaded_by
 * @property-read TaxReport $taxReport
 * @property-read User $uploader
 */
#[Fillable(['tax_report_id', 'jenis_dokumen', 'nama_file', 'drive_file_id', 'drive_file_url', 'uploaded_by'])]
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis_dokumen' => JenisDokumen::class,
        ];
    }

    /**
     * @return BelongsTo<TaxReport, $this>
     */
    public function taxReport(): BelongsTo
    {
        return $this->belongsTo(TaxReport::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
