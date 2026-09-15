<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel audit_logs dan notifications.
     */
    public function up(): void
    {
        // 1. Tabel audit_logs (Jejak Rekam Forensik & Keamanan Aktivitas Sistem)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('User pelaku aksi (NULL jika aksi otomatis sistem/cron)');
            $table->string('action', 50)->comment('Nama aksi (contoh: login, create_order, update_price, verify_technician)');
            $table->string('auditable_type', 100)->nullable()->comment('Nama model entitas yang dimodifikasi');
            $table->unsignedBigInteger('auditable_id')->nullable()->comment('Primary key entitas yang dimodifikasi');
            $table->json('old_values')->nullable()->comment('Data sebelum diubah (DILARANG menyimpan password/token)');
            $table->json('new_values')->nullable()->comment('Data setelah diubah (DILARANG menyimpan password/token)');
            $table->string('ip_address', 45)->nullable()->comment('IP Address asal request pengguna');
            $table->text('user_agent')->nullable()->comment('Informasi browser / device pengguna');
            $table->timestamp('created_at')->useCurrent()->comment('Waktu terjadinya aktivitas');

            // Index pencarian jejak audit
            $table->index('user_id');
            $table->index('action');
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('created_at');
        });

        // 2. Tabel notifications (Standar Database Notification Laravel)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('UUID unik notifikasi');
            $table->string('type')->comment('Class notifikasi Laravel');
            $table->string('notifiable_type')->comment('Polymorphic model penerima');
            $table->unsignedBigInteger('notifiable_id')->comment('ID penerima notifikasi');
            $table->text('data')->comment('Payload JSON isi pesan notifikasi');
            $table->timestamp('read_at')->nullable()->comment('Waktu notifikasi dibaca');
            $table->timestamps();

            // Index penerima notifikasi
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
    }
};
