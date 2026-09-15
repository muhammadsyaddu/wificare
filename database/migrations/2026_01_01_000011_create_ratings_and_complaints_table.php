<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel ratings dan complaints.
     */
    public function up(): void
    {
        // 1. Tabel ratings (Ulasan & Skor Kinerja Teknisi dari Pelanggan)
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi 1:1 ke order. UNIQUE menjamin 1 pesanan hanya dapat dinilai 1 kali. RESTRICT menjaga integritas histori reputasi');
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Pelanggan pemberi penilaian');
            $table->foreignId('technician_id')
                ->constrained('technician_profiles')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Teknisi penerima ulasan');
            $table->unsignedTinyInteger('rating')->comment('Skor kepuasan layanan bernilai 1 sampai 5');
            $table->text('review')->nullable()->comment('Testimoni / kritik saran tertulis dari customer');
            $table->text('response_from_technician')->nullable()->comment('Tanggapan sopan dari teknisi atas ulasan yang diberikan');
            $table->boolean('is_public')->default(true)->comment('Apakah ulasan ini ditampilkan pada profil publik teknisi');
            $table->timestamps();

            // Index pencarian rating per teknisi dan agregasi skor rata-rata
            $table->index('order_id');
            $table->index('customer_id');
            $table->index('technician_id');
            $table->index('rating');
        });

        // 2. Tabel complaints (Pengaduan Resmi Pelanggan atas Kendala Pesanan)
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_code', 30)->unique()->comment('Kode tiket pengaduan unik (contoh: CMP-202609-001)');
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Pesanan yang menjadi objek komplain');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Pelanggan pelapor kendala');
            $table->string('subject', 150)->comment('Topik ringkas pengaduan (contoh: Internet kembali mati setelah 2 jam teknisi pulang)');
            $table->text('description')->comment('Uraian rinci masalah paska pengerjaan');
            $table->enum('status', ['open', 'in_review', 'resolved', 'rejected'])
                ->default('open')
                ->comment('Status penanganan tiket komplain');
            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('Customer Service / Admin yang menyelesaikan tiket');
            $table->text('resolution_notes')->nullable()->comment('Tindakan solusi yang diambil (misal: penjadwalan garansi gratis, pengembalian dana)');
            $table->timestamp('resolved_at')->nullable()->comment('Waktu penutupan tiket komplain');
            $table->timestamps();

            // Index pencarian tiket komplain
            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('ratings');
    }
};
