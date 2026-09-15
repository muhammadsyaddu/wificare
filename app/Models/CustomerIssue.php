<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model CustomerIssue (Keluhan Masalah Pelanggan & Diagnosa Awal)
 */
class CustomerIssue extends Model
{
    use HasFactory;

    protected $table = 'customer_issues';

    protected $fillable = [
        'user_id',
        'issue_category_id',
        'title',
        'description',
        'initial_diagnosis',
    ];

    /**
     * Relasi ke user pelanggan pelapor masalah.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke kategori kendala.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(IssueCategory::class, 'issue_category_id');
    }

    /**
     * Relasi ke pesanan yang ditindaklanjuti dari keluhan ini.
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'customer_issue_id');
    }
}
