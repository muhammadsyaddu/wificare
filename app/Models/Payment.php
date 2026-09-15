<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Payment (Transaksi Pembayaran Pesanan)
 */
class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_WAITING_CONFIRMATION = 'waiting_confirmation';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REFUNDED = 'refunded';

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'payment_reference',
        'amount',
        'payment_method',
        'status',
        'gateway_provider',
        'gateway_response',
        'paid_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke pesanan induk.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relasi ke konfirmasi bukti transfer manual.
     */
    public function confirmations(): HasMany
    {
        return $this->hasMany(PaymentConfirmation::class, 'payment_id');
    }

    /**
     * Scope menyaring pembayaran yang sudah lunas.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope menyaring tagihan yang masih menunggu pelunasan.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Cek apakah pembayaran berstatus lunas.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
