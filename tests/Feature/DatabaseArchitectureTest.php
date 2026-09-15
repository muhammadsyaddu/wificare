<?php

namespace Tests\Feature;

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
use App\Models\WorkReport;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DatabaseArchitectureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_roles_and_users_structure_is_valid(): void
    {
        $this->assertDatabaseHas('roles', ['name' => Role::ADMIN]);
        $this->assertDatabaseHas('roles', ['name' => Role::TECHNICIAN]);
        $this->assertDatabaseHas('roles', ['name' => Role::CUSTOMER]);

        $admin = User::where('email', 'admin@wificare.id')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());

        $technician = User::where('email', 'eko.prasetyo@wificare.id')->first();
        $this->assertNotNull($technician);
        $this->assertTrue($technician->isTechnician());
        $this->assertNotNull($technician->technicianProfile);
        $this->assertEquals('TECH-2026-001', $technician->technicianProfile->technician_code);

        $customer = User::where('email', 'andi.pratama@gmail.com')->first();
        $this->assertNotNull($customer);
        $this->assertTrue($customer->isCustomer());
        $this->assertNotNull($customer->customerProfile);
        $this->assertGreaterThanOrEqual(1, $customer->addresses()->count());
    }

    public function test_complete_order_relationship_tree(): void
    {
        $order = Order::with([
            'customer',
            'technician',
            'items.service',
            'statusHistories',
            'assignments',
            'diagnoses',
            'workReport.attachments',
            'payments',
            'rating',
        ])->first();

        $this->assertNotNull($order);
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);

        // Header relasi
        $this->assertEquals('andi.pratama@gmail.com', $order->customer->email);
        $this->assertEquals('TECH-2026-001', $order->technician->technician_code);

        // Items & Snapshot
        $this->assertCount(2, $order->items);
        $this->assertIsArray($order->address_snapshot);
        $this->assertEquals('Perumahan Grand Wisata Blok AA3 No. 15', $order->address_snapshot['address_line']);

        // Timeline histori status
        $this->assertGreaterThanOrEqual(5, $order->statusHistories()->count());

        // Diagnosa & Laporan Kerja
        $this->assertCount(1, $order->diagnoses);
        $this->assertNotNull($order->workReport);
        $this->assertCount(3, $order->workReport->attachments);

        // Pembayaran & Rating
        $this->assertCount(1, $order->payments);
        $this->assertEquals(Payment::STATUS_PAID, $order->payments->first()->status);
        $this->assertNotNull($order->rating);
        $this->assertEquals(5, $order->rating->score);
    }

    public function test_unique_constraint_prevents_duplicate_rating_per_order(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertNotNull($order->rating);

        $this->expectException(QueryException::class);

        // Coba insert rating ke-2 untuk order yang sama
        Rating::create([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'technician_id' => $order->technician_id,
            'score' => 4,
            'review' => 'Rating duplikat spam',
        ]);
    }

    public function test_unique_constraint_prevents_duplicate_service_in_same_order(): void
    {
        $order = Order::first();
        $existingItem = $order->items->first();

        $this->expectException(QueryException::class);

        // Coba masukkan service yang sama dua kali pada 1 order
        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $existingItem->service_id,
            'quantity' => 1,
            'unit_price' => 50000.00,
            'subtotal' => 50000.00,
        ]);
    }

    public function test_user_email_must_be_unique(): void
    {
        $role = Role::first();

        $this->expectException(QueryException::class);

        User::create([
            'role_id' => $role->id,
            'name' => 'Cloned Admin',
            'email' => 'admin@wificare.id', // Sudah ada di database
            'password' => 'secret123',
        ]);
    }

    public function test_soft_deletes_preserve_record_history(): void
    {
        $service = Service::first();
        $serviceId = $service->id;

        $service->delete();

        // Data tidak hilang permanen dari database
        $this->assertSoftDeleted('services', ['id' => $serviceId]);
        $this->assertNull(Service::find($serviceId));
        $this->assertNotNull(Service::withTrashed()->find($serviceId));
    }
}
