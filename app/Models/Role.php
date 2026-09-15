<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Role (Hak Akses Pengguna)
 *
 * Mengelola peran pengguna dalam sistem WiFiCare (admin, technician, customer).
 */
class Role extends Model
{
    use HasFactory;

    public const ADMIN = 'admin';
    public const TECHNICIAN = 'technician';
    public const CUSTOMER = 'customer';

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Relasi ke seluruh pengguna yang memiliki peran ini.
     * roles 1 --- N users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
