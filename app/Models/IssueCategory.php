<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model IssueCategory (Kategori Masalah Jaringan WiFi)
 */
class IssueCategory extends Model
{
    use HasFactory;

    protected $table = 'issue_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Relasi ke seluruh keluhan pelanggan dalam kategori kendala ini.
     */
    public function customerIssues(): HasMany
    {
        return $this->hasMany(CustomerIssue::class, 'issue_category_id');
    }
}
