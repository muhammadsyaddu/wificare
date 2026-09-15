<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel customer_profiles dan addresses.
     */
    public function up(): void
    {
        // 1. Tabel customer_profiles (Profil Spesifik Pelanggan)
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi 1:1 ke users. Hapus profil jika user dihapus permanen');
            $table->string('nik', 16)->nullable()->comment('Nomor Induk Kependudukan (opsional)');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('Jenis kelamin');
            $table->date('birth_date')->nullable()->comment('Tanggal lahir pelanggan');
            $table->string('emergency_contact_name', 100)->nullable()->comment('Nama kontak darurat');
            $table->string('emergency_contact_phone', 20)->nullable()->comment('Nomor telepon kontak darurat');
            $table->text('bio')->nullable()->comment('Catatan khusus pelanggan');
            $table->timestamps();
            $table->softDeletes()->comment('Histori profil pelanggan');
        });

        // 2. Tabel addresses (Daftar Alamat Pelanggan Multi-Location)
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke user pemilik alamat');
            $table->string('label', 50)->comment('Label alamat (contoh: Rumah, Kantor, Toko, Kos)');
            $table->string('recipient_name', 100)->comment('Nama penerima / kontak di lokasi');
            $table->string('phone', 20)->comment('Nomor telepon yang dapat dihubungi di lokasi');
            $table->text('address_line')->comment('Alamat lengkap jalan, nomor bangunan, RT/RW');
            $table->string('province', 100)->comment('Provinsi');
            $table->string('city', 100)->comment('Kota / Kabupaten');
            $table->string('district', 100)->comment('Kecamatan');
            $table->string('village', 100)->nullable()->comment('Kelurahan / Desa');
            $table->string('postal_code', 10)->comment('Kode pos');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Koordinat garis lintang GPS');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Koordinat garis bujur GPS');
            $table->string('benchmark_notes', 255)->nullable()->comment('Patokan lokasi (contoh: Pagar hijau seberang masjid)');
            $table->boolean('is_default')->default(false)->comment('Apakah alamat ini alamat default pelanggan');
            $table->timestamps();
            $table->softDeletes()->comment('Alamat yang dihapus pelanggan tetap tersimpan jika sudah pernah dipakai bertransaksi');

            // Index pencarian alamat
            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('customer_profiles');
    }
};
