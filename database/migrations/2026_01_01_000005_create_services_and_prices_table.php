<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel service_categories, services, dan service_prices.
     */
    public function up(): void
    {
        // 1. Tabel service_categories (Master Kategori Layanan)
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nama kategori (contoh: Troubleshooting, Instalasi, Optimasi, Maintenance)');
            $table->string('slug', 120)->unique()->comment('Slug URL unik untuk kategori');
            $table->text('description')->nullable()->comment('Deskripsi ruang lingkup kategori');
            $table->string('icon', 100)->nullable()->comment('Nama icon class / SVG URL');
            $table->boolean('is_active')->default(true)->comment('Status ketersediaan kategori');
            $table->timestamps();
        });

        // 2. Tabel services (Master Layanan Jaringan WiFi)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')
                ->constrained('service_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi ke kategori. RESTRICT mencegah penghapusan kategori jika layanan masih ada');
            $table->string('code', 30)->unique()->comment('Kode unik layanan (contoh: SVC-WIFI-OPT)');
            $table->string('name', 150)->comment('Nama layanan');
            $table->string('slug', 180)->unique()->comment('Slug unik layanan');
            $table->text('description')->nullable()->comment('Rincian ruang lingkup pengerjaan layanan');
            $table->unsignedSmallInteger('estimated_duration_minutes')->default(60)->comment('Estimasi durasi pengerjaan dalam satuan menit');
            $table->boolean('is_active')->default(true)->comment('Status aktif penawaran layanan');
            $table->timestamps();
            $table->softDeletes()->comment('Histori layanan yang pernah ditawarkan');

            // Index pencarian layanan aktif per kategori
            $table->index('service_category_id');
            $table->index('is_active');
        });

        // 3. Tabel service_prices (Histori & Tarif Harga Layanan Berdasarkan Waktu Efektif)
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained('services')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke master layanan');
            $table->decimal('price', 12, 2)->comment('Nominal tarif layanan dalam Rupiah (DECIMAL mencegah rounding error float)');
            $table->dateTime('effective_from')->comment('Waktu mulai berlakunya tarif harga ini');
            $table->dateTime('effective_until')->nullable()->comment('Waktu berakhirnya tarif (NULL jika masih aktif berlaku)');
            $table->boolean('is_active')->default(true)->comment('Penanda cepat tarif yang sedang aktif');
            $table->string('notes', 255)->nullable()->comment('Keterangan penyesuaian tarif (misal: Promo Awal Tahun, Penyesuaian UMR)');
            $table->timestamps();

            // Index komposit untuk query harga aktif tercepat
            $table->index(['service_id', 'is_active']);
            $table->index('effective_from');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_prices');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
