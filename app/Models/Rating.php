<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Rating (Ulasan & Skor Kinerja Teknisi)
 */
class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = [
        'order_id',
        'customer_id',
        'technician_id',
        'rating',
        'score',
        'review',
        'response_from_technician',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_public' => 'boolean',
        ];
    }

    /**
     * Accessor untuk kompatibilitas properti score.
     */
    public function getScoreAttribute(): ?int
    {
        return isset($this->attributes['rating']) ? (int) $this->attributes['rating'] : null;
    }

    /**
     * Mutator untuk kompatibilitas properti score.
     */
    public function setScoreAttribute($value): void
    {
        $this->attributes['rating'] = $value;
    }

    /**
     * Relasi ke pesanan induk (1 order tepat memiliki 1 rating).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relasi ke pelanggan pemberi rating.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relasi ke teknisi yang dinilai.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(TechnicianProfile::class, 'technician_id');
    }

    /**
     * Scope menyaring ulasan publik untuk profil teknisi.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }
}
