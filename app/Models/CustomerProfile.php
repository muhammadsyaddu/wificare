<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model CustomerProfile (Profil Tambahan Khusus Pelanggan)
 */
class CustomerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_profiles';

    protected $fillable = [
        'user_id',
        'nik',
        'gender',
        'birth_date',
        'emergency_contact_name',
        'emergency_contact_phone',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Relasi balik ke akun user utama.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
