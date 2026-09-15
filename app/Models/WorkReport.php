<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model WorkReport (Laporan Resmi Hasil Pengerjaan Teknisi)
 */
class WorkReport extends Model
{
    use HasFactory;

    protected $table = 'work_reports';

    protected $fillable = [
        'order_id',
        'technician_id',
        'diagnosis_summary',
        'action_taken',
        'result',
        'technician_notes',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Relasi 1:1 ke pesanan.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relasi ke teknisi penyusun laporan.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(TechnicianProfile::class, 'technician_id');
    }

    /**
     * Relasi ke seluruh berkas foto/bukti pengerjaan.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(WorkReportAttachment::class, 'work_report_id');
    }
}
