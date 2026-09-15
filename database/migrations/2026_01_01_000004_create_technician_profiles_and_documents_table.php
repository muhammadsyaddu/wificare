<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel technician_profiles dan technician_documents.
     */
    public function up(): void
    {
        // 1. Tabel technician_profiles (Data Mitra Teknisi & Verifikasi)
        Schema::create('technician_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi 1:1 ke users. RESTRICT menjaga data histori teknisi');
            $table->string('technician_code', 30)->unique()->comment('Nomor registrasi teknisi (contoh: TECH-2026-001)');
            $table->string('phone', 20)->comment('Nomor WhatsApp / telepon operasional teknisi');
            $table->string('specialization', 150)->comment('Keahlian teknisi (contoh: FTTH, Mikrotik Routing, WiFi Mesh Optimizer)');
            $table->text('bio')->nullable()->comment('Deskripsi profil singkat pengalaman teknisi');
            $table->unsignedTinyInteger('experience_years')->default(1)->comment('Lama pengalaman kerja dalam tahun');
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'suspended'])
                ->default('pending')
                ->comment('Status legal verifikasi akun teknisi oleh admin');
            $table->timestamp('verified_at')->nullable()->comment('Waktu persetujuan verifikasi');
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('Admin yang menyetujui verifikasi');
            $table->text('rejection_reason')->nullable()->comment('Alasan penolakan dokumen jika status rejected');
            $table->boolean('is_available')->default(true)->comment('Status siap menerima tugas (true: siap, false: offline/sibuk)');
            $table->decimal('current_latitude', 10, 8)->nullable()->comment('Posisi GPS terkini teknisi');
            $table->decimal('current_longitude', 11, 8)->nullable()->comment('Posisi GPS terkini teknisi');
            $table->timestamps();
            $table->softDeletes()->comment('Histori penonaktifan akun mitra');

            // Index performa query penugasan dan ketersediaan teknisi
            $table->index('verification_status');
            $table->index('is_available');
        });

        // 2. Tabel technician_documents (Berkas Legalitas & Sertifikasi Teknisi)
        Schema::create('technician_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_profile_id')
                ->constrained('technician_profiles')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke profil teknisi pemilik dokumen');
            $table->enum('document_type', ['ktp', 'cv', 'certificate', 'sim', 'skck', 'other'])
                ->comment('Tipe dokumen verifikasi');
            $table->string('document_name', 150)->comment('Nama/keterangan dokumen (contoh: Sertifikat MTCNA)');
            $table->string('file_path', 255)->comment('Path berkas dokumen di storage');
            $table->unsignedInteger('file_size')->comment('Ukuran berkas dalam byte');
            $table->string('mime_type', 50)->comment('Tipe MIME berkas (contoh: application/pdf, image/jpeg)');
            $table->boolean('is_verified')->default(false)->comment('Apakah berkas ini sudah divalidasi keasliannya oleh admin');
            $table->string('notes', 255)->nullable()->comment('Catatan catatan verifikator terhadap berkas');
            $table->timestamps();

            // Index pencarian berkas berdasarkan teknisi & tipe dokumen
            $table->index(['technician_profile_id', 'document_type']);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_documents');
        Schema::dropIfExists('technician_profiles');
    }
};
