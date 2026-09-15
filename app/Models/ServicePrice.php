<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ServicePrice (Histori & Versi Tarif Layanan)
 */
class ServicePrice extends Model
{
    use HasFactory;

    protected $table = 'service_prices';

    protected $fillable = [
        'service_id',
        'price',
        'effective_from',
        'effective_until',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'effective_from' => 'datetime',
            'effective_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke master layanan pemilik harga.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Scope menyaring harga yang saat ini aktif berlaku.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
