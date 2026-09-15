<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Diagnosis (Catatan Pemeriksaan Teknis Jaringan di Lapangan)
 */
class Diagnosis extends Model
{
    use HasFactory;

    protected $table = 'diagnoses';

    protected $fillable = [
        'order_id',
        'technician_id',
        'problem_found',
        'diagnosis_result',
        'network_condition',
        'recommendation',
        'diagnosed_at',
    ];

    protected function casts(): array
    {
        return [
            'diagnosed_at' => 'datetime',
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
     * Relasi ke teknisi yang melakukan diagnosa.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(TechnicianProfile::class, 'technician_id');
    }
}
