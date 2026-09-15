<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model WorkReportAttachment (Foto & Dokumen Bukti Pekerjaan)
 */
class WorkReportAttachment extends Model
{
    use HasFactory;

    protected $table = 'work_report_attachments';

    protected $fillable = [
        'work_report_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'attachment_type',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    /**
     * Relasi ke laporan kerja induk.
     */
    public function workReport(): BelongsTo
    {
        return $this->belongsTo(WorkReport::class, 'work_report_id');
    }

    /**
     * Relasi ke user yang mengunggah berkas.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
