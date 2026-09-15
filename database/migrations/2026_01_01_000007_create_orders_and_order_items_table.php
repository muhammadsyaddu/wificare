<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel orders dan order_items.
     */
    public function up(): void
    {
        // 1. Tabel orders (Pusat Transaksi Pemesanan Layanan WiFiCare)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique()->comment('Nomor unik faktur transaksi (contoh: ORD-20260907-0001)');
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi ke user pelanggan pemesan. RESTRICT mencegah penghapusan user yang memiliki transaksi aktif');
            $table->foreignId('technician_id')
                ->nullable()
                ->constrained('technician_profiles')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('Teknisi definitif yang ditugaskan');
            $table->foreignId('address_id')
                ->constrained('addresses')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi ke alamat tujuan layanan');
            $table->json('address_snapshot')->comment('Snapshot JSON data alamat lengkap saat transaksi dibuat agar perubahan di profil tidak merusak data histori');
            $table->foreignId('customer_issue_id')
                ->nullable()
                ->constrained('customer_issues')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('Relasi opsional ke keluhan awal yang memicu booking');
            $table->dateTime('scheduled_at')->comment('Waktu jadwal kedatangan teknisi yang disepakati');
            $table->enum('status', [
                'pending',
                'confirmed',
                'assigned',
                'accepted',
                'on_the_way',
                'in_progress',
                'diagnosis',
                'repairing',
                'completed',
                'cancelled'
            ])->default('pending')->comment('Tahapan status operasional order');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Total akumulasi biaya item layanan');
            $table->decimal('service_fee', 12, 2)->default(0)->comment('Biaya transportasi teknisi / platform fee');
            $table->decimal('discount', 12, 2)->default(0)->comment('Potongan voucher / promo');
            $table->decimal('total_amount', 12, 2)->default(0)->comment('Total tagihan akhir yang wajib dibayar (subtotal + service_fee - discount)');
            $table->text('notes')->nullable()->comment('Catatan instruksi khusus dari customer untuk teknisi');
            $table->text('cancellation_reason')->nullable()->comment('Alasan pembatalan pesanan jika status cancelled');
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('Pihak yang membatalkan (Customer / Admin / Teknisi)');
            
            // Kolom audit timestamp tahapan alur bisnis
            $table->timestamp('confirmed_at')->nullable()->comment('Waktu order dikonfirmasi admin/sistem');
            $table->timestamp('accepted_at')->nullable()->comment('Waktu teknisi menerima tugas');
            $table->timestamp('started_at')->nullable()->comment('Waktu teknisi tiba dan mulai bekerja');
            $table->timestamp('completed_at')->nullable()->comment('Waktu pekerjaan dinyatakan selesai');
            $table->timestamp('cancelled_at')->nullable()->comment('Waktu pembatalan pesanan');
            $table->timestamps();

            // Index pencarian dan filter dashboard intensif
            $table->index('order_number');
            $table->index('customer_id');
            $table->index('technician_id');
            $table->index('status');
            $table->index('scheduled_at');
            $table->index(['customer_id', 'status']);
            $table->index(['technician_id', 'status']);
        });

        // 2. Tabel order_items (Rincian Layanan & Snapshot Harga Transaksi)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke order induk. CASCADE wajar karena order_items adalah dependent entity dari orders');
            $table->foreignId('service_id')
                ->constrained('services')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi ke master layanan. RESTRICT mencegah penghapusan katalog yang pernah dibeli');
            $table->unsignedSmallInteger('quantity')->default(1)->comment('Jumlah unit pengerjaan (misal: 1 paket, 2 titik AP, dsb)');
            $table->decimal('unit_price', 12, 2)->comment('SNAPSHOT HARGA satuan saat order dibuat (tidak boleh berubah jika harga master naik)');
            $table->decimal('subtotal', 12, 2)->comment('Perhitungan kuantitas dikalikan unit_price');
            $table->string('notes', 255)->nullable()->comment('Catatan spesifik pengerjaan item layanan ini');
            $table->timestamps();

            // Index performa query item per order & pelaporan frekuensi layanan
            $table->index('order_id');
            $table->index('service_id');
            $table->unique(['order_id', 'service_id'], 'uq_order_items_order_service');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
