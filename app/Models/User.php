<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User (Autentikasi Utama Pengguna)
 *
 * Mendukung multi-role (Admin, Teknisi, Pelanggan) dengan pemisahan profil
 * dan perlindungan histori menggunakan SoftDeletes.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar_url',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke Master Role pengguna.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Relasi 1:1 ke profil pelanggan (khusus role customer).
     */
    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class, 'user_id');
    }

    /**
     * Relasi 1:N ke buku alamat pelanggan.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    /**
     * Relasi 1:1 ke profil teknisi profesional (khusus role technician).
     */
    public function technicianProfile(): HasOne
    {
        return $this->hasOne(TechnicianProfile::class, 'user_id');
    }

    /**
     * Relasi 1:N ke laporan kendala awal yang pernah dibuat user.
     */
    public function customerIssues(): HasMany
    {
        return $this->hasMany(CustomerIssue::class, 'user_id');
    }

    /**
     * Relasi 1:N ke seluruh transaksi pesanan sebagai pelanggan.
     */
    public function ordersAsCustomer(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Relasi 1:N ke rating ulasan yang diberikan pelanggan.
     */
    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'customer_id');
    }

    /**
     * Relasi 1:N ke tiket pengaduan yang diajukan pelanggan.
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }

    /**
     * Relasi 1:N ke rekaman jejak audit sistem.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    // ==========================================
    // BUSINESS HELPER METHODS
    // ==========================================

    /**
     * Periksa apakah user memiliki peran Administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    /**
     * Periksa apakah user memiliki peran Teknisi Lapangan.
     */
    public function isTechnician(): bool
    {
        return $this->role?->name === 'technician';
    }

    /**
     * Periksa apakah user memiliki peran Pelanggan.
     */
    public function isCustomer(): bool
    {
        return $this->role?->name === 'customer';
    }

    /**
     * Periksa peran user berdasarkan nama peran.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }
}
