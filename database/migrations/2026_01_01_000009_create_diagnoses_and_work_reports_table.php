<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel diagnoses, work_reports, dan work_report_attachments.
     */
    public function up(): void
    {
        // 1. Tabel diagnoses (Catatan Pemeriksaan Teknis Jaringan di Lokasi Customer)
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke order terkait');
            $table->foreignId('technician_id')
                ->constrained('technician_profiles')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Teknisi pemeriksa');
            $table->text('problem_found')->comment('Akar masalah teknis yang ditemukan teknisi');
            $table->text('diagnosis_result')->comment('Uraian teknis kondisi kabel, redaman fiber optik, interferensi frekuensi, dsb');
            $table->text('network_condition')->nullable()->comment('Parameter metrik jaringan (misal: RSSI -75 dBm, Ping 18ms, Jitter 3ms, Redaman -21 dBm)');
            $table->text('recommendation')->nullable()->comment('Saran teknis bagi customer (misal: ganti kabel Cat6, tambah access point mesh)');
            $table->dateTime('diagnosed_at')->comment('Waktu diagnosa dilakukan di lokasi');
            $table->timestamps();

            // Index pencarian diagnosa per order dan teknisi
            $table->index('order_id');
            $table->index('technician_id');
        });

        // 2. Tabel work_reports (Laporan Resmi Penyelesaian Pengerjaan Teknisi)
        Schema::create('work_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi 1:1 ke order. UNIQUE menjamin hanya ada 1 laporan final per order. RESTRICT melindungi berkas pengerjaan');
            $table->foreignId('technician_id')
                ->constrained('technician_profiles')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Teknisi penyusun laporan');
            $table->text('diagnosis_summary')->comment('Ringkasan diagnosa awal');
            $table->text('action_taken')->comment('Langkah dan tindakan teknis yang telah dikerjakan (contoh: crimping ulang RJ45, tuning kanal WiFi)');
            $table->text('result')->comment('Kondisi akhir jaringan setelah perbaikan (contoh: throughput naik ke 85 Mbps, latency stabil)');
            $table->text('technician_notes')->nullable()->comment('Catatan edukasi atau rekomendasi garansi dari teknisi');
            $table->dateTime('started_at')->comment('Waktu mulai pengerjaan fisik');
            $table->dateTime('completed_at')->comment('Waktu selesai pekerjaan fisik');
            $table->timestamps();

            // Index pencarian laporan kerja
            $table->index('order_id');
            $table->index('technician_id');
        });

        // 3. Tabel work_report_attachments (Foto & Bukti Digital Pengerjaan Lapangan)
        Schema::create('work_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_report_id')
                ->constrained('work_reports')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke laporan kerja induk. CASCADE menghapus lampiran jika laporan di-purge');
            $table->string('file_path', 255)->comment('Path penyimpanan file di storage sistem');
            $table->string('file_name', 255)->comment('Nama asli file yang diunggah');
            $table->unsignedInteger('file_size')->comment('Ukuran file dalam bytes');
            $table->string('mime_type', 100)->comment('MIME type file (contoh: image/jpeg, image/png)');
            $table->enum('attachment_type', ['before', 'in_progress', 'after', 'signature', 'speedtest', 'other'])
                ->default('other')
                ->comment('Kategori dokumentasi bukti pengerjaan');
            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('User yang mengunggah berkas');
            $table->timestamps();

            // Index pencarian attachment per laporan dan jenis dokumentasi
            $table->index('work_report_id');
            $table->index('attachment_type');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_report_attachments');
        Schema::dropIfExists('work_reports');
        Schema::dropIfExists('diagnoses');
    }
};
