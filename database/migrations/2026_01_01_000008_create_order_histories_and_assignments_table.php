<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel order_status_histories dan technician_assignments.
     */
    public function up(): void
    {
        // 1. Tabel order_status_histories (Jejak Rekam Kronologis Perubahan Status Order)
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke order terkait');
            $table->string('status', 30)->comment('Nilai status pada tahap ini');
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('User yang melakukan perubahan status');
            $table->text('notes')->nullable()->comment('Keterangan konteks saat status diperbarui');
            $table->timestamps();

            // Index timeline audit status order
            $table->index('order_id');
            $table->index(['order_id', 'created_at']);
        });

        // 2. Tabel technician_assignments (Riwayat Penugasan, Penerimaan & Penolakan Teknisi)
        Schema::create('technician_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke pesanan yang ditugaskan');
            $table->foreignId('technician_id')
                ->constrained('technician_profiles')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Teknisi yang didelegasikan tugas');
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('Admin penugasan (NULL jika assignment otomatis sistem)');
            $table->dateTime('assigned_at')->comment('Waktu penugasan dikirimkan ke teknisi');
            $table->dateTime('accepted_at')->nullable()->comment('Waktu penugasan diterima teknisi');
            $table->dateTime('rejected_at')->nullable()->comment('Waktu penugasan ditolak teknisi');
            $table->text('rejected_reason')->nullable()->comment('Alasan teknisi menolak penugasan (misal: jarak terlalu jauh, ada kendala motor)');
            $table->dateTime('unassigned_at')->nullable()->comment('Waktu pembatalan delegasi oleh admin jika teknisi diganti');
            $table->text('unassigned_reason')->nullable()->comment('Alasan pergantian teknisi');
            $table->timestamps();

            // Index pencarian riwayat penugasan
            $table->index('order_id');
            $table->index('technician_id');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_assignments');
        Schema::dropIfExists('order_status_histories');
    }
};
