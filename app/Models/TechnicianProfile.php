<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model TechnicianProfile (Profil Profesional Mitra Teknisi)
 */
class TechnicianProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'technician_profiles';

    protected $fillable = [
        'user_id',
        'technician_code',
        'phone',
        'specialization',
        'bio',
        'experience_years',
        'verification_status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'is_available',
        'current_latitude',
        'current_longitude',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'verified_at' => 'datetime',
            'is_available' => 'boolean',
            'current_latitude' => 'decimal:8',
            'current_longitude' => 'decimal:8',
        ];
    }

    /**
     * Relasi ke akun user utama.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke admin yang memverifikasi akun ini.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relasi ke dokumen sertifikasi / identitas teknisi.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(TechnicianDocument::class, 'technician_profile_id');
    }

    /**
     * Relasi ke seluruh pesanan yang ditangani teknisi ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'technician_id');
    }

    /**
     * Relasi ke riwayat penugasan pekerjaan.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TechnicianAssignment::class, 'technician_id');
    }

    /**
     * Relasi ke catatan diagnosa teknisi di lapangan.
     */
    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class, 'technician_id');
    }

    /**
     * Relasi ke laporan penyelesaian pengerjaan teknisi.
     */
    public function workReports(): HasMany
    {
        return $this->hasMany(WorkReport::class, 'technician_id');
    }

    /**
     * Relasi ke seluruh rating ulasan yang diterima dari pelanggan.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'technician_id');
    }

    /**
     * Scope menyaring teknisi yang sudah terverifikasi resmi.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verification_status', 'verified');
    }

    /**
     * Scope menyaring teknisi yang sedang aktif dan siap menerima penugasan.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('verification_status', 'verified')
                     ->where('is_available', true);
    }

    /**
     * Nilai rata-rata skor rating teknisi.
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->ratings()->avg('rating') ?? 0);
    }
}
