<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel roles, users, password_reset_tokens, dan sessions.
     */
    public function up(): void
    {
        // 1. Tabel roles (Master Hak Akses Pengguna: admin, technician, customer)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Nama teknis role (contoh: admin, technician, customer)');
            $table->string('display_name', 100)->comment('Nama label tampilan untuk UI (contoh: Administrator)');
            $table->text('description')->nullable()->comment('Deskripsi wewenang role');
            $table->timestamps();
        });

        // 2. Tabel users (Tabel Utama Autentikasi Pengguna Multi-Role)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                ->constrained('roles')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi ke master role. RESTRICT mencegah penghapusan role jika user aktif masih ada');
            $table->string('name', 150)->comment('Nama lengkap user');
            $table->string('email', 150)->unique()->comment('Email unik untuk login');
            $table->string('phone', 20)->nullable()->comment('Nomor kontak utama user');
            $table->timestamp('email_verified_at')->nullable()->comment('Waktu verifikasi email akun');
            $table->string('password')->comment('Password yang di-hash dengan algoritma Bcrypt/Argon2id');
            $table->string('avatar_url', 255)->nullable()->comment('Path foto profil avatar');
            $table->boolean('is_active')->default(true)->comment('Status keaktifan akun user (true: aktif, false: dinonaktifkan)');
            $table->rememberToken()->comment('Token sesi remember-me Laravel');
            $table->timestamps();
            $table->softDeletes()->comment('Menyimpan histori penghapusan akun tanpa merusak relasi transaksi');

            // Index tambahan untuk pencarian dan filter cepat
            $table->index('is_active');
            $table->index('role_id');
        });

        // 3. Tabel password_reset_tokens (Standar Keamanan Reset Password Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 150)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 4. Tabel sessions (Penyimpanan Sesi Berbasis Database Laravel)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
