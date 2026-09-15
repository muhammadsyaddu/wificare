<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model TechnicianDocument (Berkas Kelayakan & Sertifikasi Teknisi)
 */
class TechnicianDocument extends Model
{
    use HasFactory;

    protected $table = 'technician_documents';

    protected $fillable = [
        'technician_profile_id',
        'document_type',
        'document_name',
        'file_path',
        'file_size',
        'mime_type',
        'is_verified',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Relasi ke profil teknisi pemilik berkas.
     */
    public function technicianProfile(): BelongsTo
    {
        return $this->belongsTo(TechnicianProfile::class, 'technician_profile_id');
    }
}
