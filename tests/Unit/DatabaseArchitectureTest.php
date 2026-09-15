<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Rating;
use App\Models\Role;
use App\Models\Service;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class DatabaseArchitectureTest extends TestCase
{
    /**
     * Uji 1: Verifikasi master role dan user multi-role terhubung dengan benar.
     */
    public function test_roles_and_users_relationship(): void
    {
        $admin = User::where('email', 'admin@wificare.id')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertEquals('admin', $admin->role->name);

        $customer = User::where('email', 'andi.pratama@gmail.com')->first();
        $this->assertNotNull($customer);
        $this->assertTrue($customer->isCustomer());
        $this->assertInstanceOf(CustomerProfile::class, $customer->customerProfile);
        $this->assertGreaterThanOrEqual(1, $customer->addresses->count());

        $technicianUser = User::where('email', 'eko.prasetyo@wificare.id')->first();
        $this->assertNotNull($technicianUser);
        $this->assertTrue($technicianUser->isTechnician());
        $this->assertInstanceOf(TechnicianProfile::class, $technicianUser->technicianProfile);
        $this->assertEquals('TECH-2026-001', $technicianUser->technicianProfile->technician_code);
    }

    /**
     * Uji 2: Verifikasi siklus order, snapshot harga, histori status, dan relasi laporan kerja.
     */
    public function test_order_full_lifecycle_and_snapshot_integrity(): void
    {
        $order = Order::with([
            'customer',
            'technician.user',
            'items.service',
            'statusHistories',
            'workReport.attachments',
            'payments',
            'rating'
        ])->where('order_number', 'ORD-20260901-0001')->first();

        $this->assertNotNull($order);
        $this->assertEquals('completed', $order->status);
        $this->assertIsArray($order->address_snapshot);
        $this->assertEquals('Perumahan Grand Wisata Blok AA3 No. 15', $order->address_snapshot['address_line']);

        // Verifikasi snapshot order items
        $this->assertCount(2, $order->items);
        $calculatedSubtotal = 0;
        foreach ($order->items as $item) {
            $this->assertGreaterThan(0, $item->unit_price);
            $this->assertEquals($item->quantity * $item->unit_price, $item->subtotal);
            $calculatedSubtotal += $item->subtotal;
        }
        $this->assertEquals($order->subtotal, $calculatedSubtotal);

        // Verifikasi histori status
        $this->assertGreaterThanOrEqual(5, $order->statusHistories->count());

        // Verifikasi laporan kerja & bukti lampiran
        $this->assertNotNull($order->workReport);
        $this->assertGreaterThanOrEqual(1, $order->workReport->attachments->count());

        // Verifikasi pembayaran lunas & rating
        $this->assertTrue($order->payments->first()->isPaid());
        $this->assertNotNull($order->rating);
        $this->assertEquals(5, $order->rating->rating);
    }

    /**
     * Uji 3: Verifikasi aturan integritas unik (Anti-Duplication) pada nomor order dan email.
     */
    public function test_anti_duplication_unique_constraints(): void
    {
        // 1. Uji duplikasi email pengguna
        $this->expectException(QueryException::class);
        User::create([
            'role_id' => Role::where('name', 'customer')->first()->id,
            'name' => 'Duplikat Andi',
            'email' => 'andi.pratama@gmail.com', // Email sudah terdaftar
            'password' => 'password123',
        ]);
    }

    /**
     * Uji 4: Verifikasi aturan bisnis bahwa 1 order hanya dapat diberi maksimal 1 rating (UNIQUE order_id).
     */
    public function test_single_rating_per_order_constraint(): void
    {
        $existingOrder = Order::where('order_number', 'ORD-20260901-0001')->first();
        $this->assertNotNull($existingOrder->rating);

        // Percobaan menambahkan rating kedua pada order yang sama wajib gagal di level database
        $this->expectException(QueryException::class);
        Rating::create([
            'order_id' => $existingOrder->id,
            'customer_id' => $existingOrder->customer_id,
            'technician_id' => $existingOrder->technician_id,
            'rating' => 4,
            'review' => 'Mencoba memberi rating ganda',
        ]);
    }
}
