<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Order (Pusat Transaksi Pemesanan WiFiCare)
 */
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_ON_THE_WAY = 'on_the_way';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DIAGNOSIS = 'diagnosis';
    public const STATUS_REPAIRING = 'repairing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'customer_id',
        'technician_id',
        'address_id',
        'address_snapshot',
        'customer_issue_id',
        'scheduled_at',
        'status',
        'subtotal',
        'service_fee',
        'discount',
        'total_amount',
        'notes',
        'cancellation_reason',
        'cancelled_by',
        'confirmed_at',
        'accepted_at',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'address_snapshot' => 'array',
            'scheduled_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke pelanggan pemesan.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relasi ke teknisi definitif yang mengerjakan pesanan.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(TechnicianProfile::class, 'technician_id');
    }

    /**
     * Relasi ke alamat tujuan master.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Relasi ke keluhan awal (jika pemesanan diawali dari diagnosa mandiri).
     */
    public function customerIssue(): BelongsTo
    {
        return $this->belongsTo(CustomerIssue::class, 'customer_issue_id');
    }

    /**
     * Relasi ke user yang membatalkan order.
     */
    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Relasi ke rincian layanan pesanan.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Relasi ke log histori perubahan status pesanan.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id')->orderBy('created_at', 'asc');
    }

    /**
     * Relasi ke riwayat penugasan teknisi.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TechnicianAssignment::class, 'order_id')->orderBy('assigned_at', 'desc');
    }

    /**
     * Relasi ke pemeriksaan diagnosa lapangan teknisi.
     */
    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class, 'order_id');
    }

    /**
     * Relasi 1:1 ke laporan penyelesaian pekerjaan final.
     */
    public function workReport(): HasOne
    {
        return $this->hasOne(WorkReport::class, 'order_id');
    }

    /**
     * Relasi ke tagihan dan catatan pembayaran transaksi ini.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    /**
     * Mengambil pembayaran terakhir.
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id')->latestOfMany();
    }

    /**
     * Relasi 1:0..1 ke rating & review pelanggan.
     */
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class, 'order_id');
    }

    /**
     * Relasi ke tiket pengaduan atas pesanan ini.
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'order_id');
    }

    // ==========================================
    // SCOPES & HELPER BISNIS
    // ==========================================

    /**
     * Scope filter berdasarkan status operasional.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope filter pesanan milik customer tertentu.
     */
    public function scopeForCustomer(Builder $query, int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope filter pesanan yang ditugaskan ke teknisi tertentu.
     */
    public function scopeForTechnician(Builder $query, int $technicianId): Builder
    {
        return $query->where('technician_id', $technicianId);
    }

    /**
     * Cek apakah pekerjaan sudah selesai tuntas.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Cek apakah order dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cek apakah order memenuhi syarat untuk diberi rating ulasan oleh pelanggan.
     */
    public function canBeRated(): bool
    {
        return $this->isCompleted() && ! $this->relationLoaded('rating') ? is_null($this->rating()->first()) : is_null($this->rating);
    }
}
