<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel issue_categories dan customer_issues.
     */
    public function up(): void
    {
        // 1. Tabel issue_categories (Master Klasifikasi Kendala Jaringan WiFi)
        Schema::create('issue_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nama kategori kendala (contoh: WiFi Lambat, Sinyal Drop, Router Mati)');
            $table->string('slug', 120)->unique()->comment('Slug unik kategori kendala');
            $table->text('description')->nullable()->comment('Penjelasan indikasi kendala');
            $table->timestamps();
        });

        // 2. Tabel customer_issues (Pelaporan Masalah & Hasil Diagnosa Awal Pelanggan)
        Schema::create('customer_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke user pelapor masalah');
            $table->foreignId('issue_category_id')
                ->constrained('issue_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi ke kategori kendala. RESTRICT menjaga integritas histori pengaduan');
            $table->string('title', 150)->comment('Judul singkat masalah yang dialami (contoh: Sinyal WiFi di lantai 2 tidak terdeteksi)');
            $table->text('description')->comment('Deskripsi lengkap gejala kerusakan jaringan WiFi');
            $table->text('initial_diagnosis')->nullable()->comment('Hasil rekomendasi / diagnosa otomatis sistem pra-order');
            $table->timestamps();

            // Index pencarian keluhan per user dan per kategori
            $table->index('user_id');
            $table->index('issue_category_id');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_issues');
        Schema::dropIfExists('issue_categories');
    }
};
