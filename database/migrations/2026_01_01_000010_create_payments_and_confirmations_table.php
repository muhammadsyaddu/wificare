<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel payments dan payment_confirmations.
     */
    public function up(): void
    {
        // 1. Tabel payments (Transaksi Finansial Tagihan Layanan)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onUpdate('cascade')
                ->onDelete('restrict')
                ->comment('Relasi ke order terkait. RESTRICT melindungi riwayat mutasi pembayaran');
            $table->string('payment_reference', 50)->unique()->comment('Kode unik referensi pembayaran (contoh: PAY-20260907-XXXX)');
            $table->decimal('amount', 12, 2)->comment('Nominal kewajiban pembayaran');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qris', 'e_wallet', 'gateway'])
                ->default('qris')
                ->comment('Kanal metode pembayaran');
            $table->enum('status', ['pending', 'waiting_confirmation', 'paid', 'failed', 'expired', 'refunded'])
                ->default('pending')
                ->comment('Status siklus hidup pembayaran');
            $table->string('gateway_provider', 50)->nullable()->comment('Nama payment gateway jika menggunakan otomatis (contoh: Midtrans, Xendit)');
            $table->json('gateway_response')->nullable()->comment('Log respon mentah webhook gateway');
            $table->timestamp('paid_at')->nullable()->comment('Waktu pelunasan sukses tercatat');
            $table->timestamp('expired_at')->nullable()->comment('Batas waktu pembayaran');
            $table->timestamps();

            // Index pencarian transaksi pembayaran dan rekapitulasi keuangan
            $table->index('order_id');
            $table->index('status');
            $table->index('paid_at');
        });

        // 2. Tabel payment_confirmations (Bukti Transfer Manual & Histori Verifikasi Finansial)
        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')
                ->constrained('payments')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Relasi ke record pembayaran terkait');
            $table->string('bank_name', 50)->comment('Nama bank pengirim (contoh: BCA, Mandiri, BRI)');
            $table->string('account_number', 50)->comment('Nomor rekening pengirim');
            $table->string('account_holder_name', 100)->comment('Nama pemilik rekening pengirim');
            $table->decimal('transfer_amount', 12, 2)->comment('Nominal dana yang ditransfer');
            $table->date('transfer_date')->comment('Tanggal pelaksanaan transfer');
            $table->string('proof_file_path', 255)->comment('Path foto/struk bukti transfer');
            $table->enum('status', ['pending', 'verified', 'rejected'])
                ->default('pending')
                ->comment('Status verifikasi pembayaran oleh bendahara / admin');
            $table->text('notes')->nullable()->comment('Catatan verifikasi atau alasan penolakan bukti transfer');
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null')
                ->comment('Admin yang memverifikasi mutasi');
            $table->timestamp('verified_at')->nullable()->comment('Waktu verifikasi sukses');
            $table->timestamps();

            // Index filter verifikasi pembayaran pending
            $table->index('payment_id');
            $table->index('status');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_confirmations');
        Schema::dropIfExists('payments');
    }
};
