<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Service (Katalog Layanan Jaringan WiFi)
 */
class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'services';

    protected $fillable = [
        'service_category_id',
        'code',
        'name',
        'slug',
        'description',
        'estimated_duration_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'estimated_duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke kategori induk.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * Relasi ke seluruh riwayat harga historis.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ServicePrice::class, 'service_id');
    }

    /**
     * Relasi ke harga aktif terkini.
     */
    public function activePrice(): HasOne
    {
        return $this->hasOne(ServicePrice::class, 'service_id')
            ->where('is_active', true)
            ->latestOfMany('effective_from');
    }

    /**
     * Relasi ke seluruh item order yang pernah memesan layanan ini.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'service_id');
    }

    /**
     * Scope menyaring layanan yang aktif ditawarkan.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Mengambil tarif harga aktif saat ini secara langsung.
     */
    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->activePrice?->price ?? 0);
    }
}
