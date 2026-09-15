<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model TechnicianAssignment (Riwayat Penugasan & Respon Teknisi)
 */
class TechnicianAssignment extends Model
{
    use HasFactory;

    protected $table = 'technician_assignments';

    protected $fillable = [
        'order_id',
        'technician_id',
        'assigned_by',
        'assigned_at',
        'accepted_at',
        'rejected_at',
        'rejected_reason',
        'unassigned_at',
        'unassigned_reason',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'unassigned_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke pesanan yang ditugaskan.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relasi ke teknisi yang ditugaskan.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(TechnicianProfile::class, 'technician_id');
    }

    /**
     * Relasi ke user admin yang menugaskan.
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
