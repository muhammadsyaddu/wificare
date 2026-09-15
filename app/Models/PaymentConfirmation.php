<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model PaymentConfirmation (Bukti Transfer & Verifikasi Keuangan)
 */
class PaymentConfirmation extends Model
{
    use HasFactory;

    protected $table = 'payment_confirmations';

    protected $fillable = [
        'payment_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'transfer_amount',
        'transfer_date',
        'proof_file_path',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'transfer_amount' => 'decimal:2',
            'transfer_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke transaksi pembayaran induk.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Relasi ke admin verifikator mutasi bank.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
