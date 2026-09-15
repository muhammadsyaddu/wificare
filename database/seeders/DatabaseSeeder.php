<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AuditLog;
use App\Models\Complaint;
use App\Models\CustomerIssue;
use App\Models\CustomerProfile;
use App\Models\Diagnosis;
use App\Models\IssueCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PaymentConfirmation;
use App\Models\Rating;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServicePrice;
use App\Models\TechnicianAssignment;
use App\Models\TechnicianDocument;
use App\Models\TechnicianProfile;
use App\Models\User;
use App\Models\WorkReport;
use App\Models\WorkReportAttachment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. ROLES
        // =========================================================================
        $roleAdmin = Role::create([
            'name' => Role::ADMIN,
            'display_name' => 'Administrator',
            'description' => 'Pengelola sistem, verifikator mitra teknisi, pemantau order, dan pengawas transaksi keuangan.',
        ]);

        $roleTechnician = Role::create([
            'name' => Role::TECHNICIAN,
            'display_name' => 'Teknisi Lapangan',
            'description' => 'Mitra teknisi profesional pengerjaan perbaikan, instalasi, dan optimasi jaringan WiFi.',
        ]);

        $roleCustomer = Role::create([
            'name' => Role::CUSTOMER,
            'display_name' => 'Pelanggan',
            'description' => 'Pengguna jasa layanan perawatan WiFi rumah, kantor, kos, dan cafe.',
        ]);

        // =========================================================================
        // 2. USERS (Multi-Role)
        // =========================================================================
        $defaultPassword = Hash::make('password123');

        // Admin
        $admin = User::create([
            'role_id' => $roleAdmin->id,
            'name' => 'Fajar Pratama (Admin)',
            'email' => 'admin@wificare.id',
            'phone' => '081122334455',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Teknisi 1 (Eko Prasetyo - TECH-2026-001)
        $userTech1 = User::create([
            'role_id' => $roleTechnician->id,
            'name' => 'Eko Prasetyo',
            'email' => 'eko.prasetyo@wificare.id',
            'phone' => '081234567890',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Teknisi 2 (Budi Santoso - TECH-2026-002)
        $userTech2 = User::create([
            'role_id' => $roleTechnician->id,
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@wificare.id',
            'phone' => '081987654321',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Customer 1 (Andi Pratama)
        $customer1 = User::create([
            'role_id' => $roleCustomer->id,
            'name' => 'Andi Pratama',
            'email' => 'andi.pratama@gmail.com',
            'phone' => '085712345678',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Customer 2 (Siti Rahmawati)
        $customer2 = User::create([
            'role_id' => $roleCustomer->id,
            'name' => 'Siti Rahmawati',
            'email' => 'siti.customer@gmail.com',
            'phone' => '087887654321',
            'password' => $defaultPassword,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // =========================================================================
        // 3. CUSTOMER PROFILES & ADDRESSES
        // =========================================================================
        CustomerProfile::create([
            'user_id' => $customer1->id,
            'nik' => '3273011405980001',
            'gender' => 'male',
            'birth_date' => '1998-05-14',
            'emergency_contact_name' => 'Rina (Istri)',
            'emergency_contact_phone' => '085799887766',
            'bio' => 'Rumah 2 lantai di Grand Wisata, menggunakan ISP 50 Mbps untuk WFH software engineering.',
        ]);

        $addressAndiHome = Address::create([
            'user_id' => $customer1->id,
            'label' => 'Rumah Utama',
            'recipient_name' => 'Andi Pratama',
            'phone' => '085712345678',
            'address_line' => 'Perumahan Grand Wisata Blok AA3 No. 15',
            'province' => 'Jawa Barat',
            'city' => 'Bekasi',
            'district' => 'Tambun Selatan',
            'village' => 'Lambangjaya',
            'postal_code' => '17510',
            'latitude' => -6.28245000,
            'longitude' => 107.05541000,
            'benchmark_notes' => 'Pagar hitam depan taman cluster',
            'is_default' => true,
        ]);

        Address::create([
            'user_id' => $customer1->id,
            'label' => 'Kantor Studio',
            'recipient_name' => 'Andi Pratama',
            'phone' => '085712345678',
            'address_line' => 'Ruko Sinpasa Commercial Blok C No. 8',
            'province' => 'Jawa Barat',
            'city' => 'Bekasi',
            'district' => 'Bekasi Barat',
            'village' => 'Marga Jaya',
            'postal_code' => '17141',
            'latitude' => -6.24140000,
            'longitude' => 106.99870000,
            'benchmark_notes' => 'Ruko lantai 2 samping bank BCA',
            'is_default' => false,
        ]);

        CustomerProfile::create([
            'user_id' => $customer2->id,
            'nik' => '3273026208010002',
            'gender' => 'female',
            'birth_date' => '2001-08-22',
            'emergency_contact_name' => 'Bapak Surya',
            'emergency_contact_phone' => '081322110099',
            'bio' => 'Mahasiswi tingkat akhir di Bandung, kos sering mengalami WiFi lemot.',
        ]);

        $addressSitiKos = Address::create([
            'user_id' => $customer2->id,
            'label' => 'Kos Mahasiswa',
            'recipient_name' => 'Siti Rahmawati',
            'phone' => '087887654321',
            'address_line' => 'Jl. Dago Pojok No. 12B, Kamar No. 04',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bandung',
            'district' => 'Coblong',
            'village' => 'Dago',
            'postal_code' => '40135',
            'latitude' => -6.87912000,
            'longitude' => 107.61634000,
            'benchmark_notes' => 'Pintu gerbang putih, masuk gang samping apotek',
            'is_default' => true,
        ]);

        // =========================================================================
        // 4. TECHNICIAN PROFILES & DOCUMENTS
        // =========================================================================
        $techProfile1 = TechnicianProfile::create([
            'user_id' => $userTech1->id,
            'technician_code' => 'TECH-2026-001',
            'phone' => '081234567890',
            'specialization' => 'FTTH, Mikrotik Routing, WiFi Mesh Optimizer, Splicing Fiber Optic',
            'bio' => 'Teknisi senior 4 tahun menangani infrastruktur jaringan perumahan dan perkantoran.',
            'experience_years' => 4,
            'verification_status' => 'verified',
            'verified_at' => now()->subMonths(3),
            'verified_by' => $admin->id,
            'is_available' => true,
            'current_latitude' => -6.28000000,
            'current_longitude' => 107.05000000,
        ]);

        TechnicianDocument::create([
            'technician_profile_id' => $techProfile1->id,
            'document_type' => 'ktp',
            'document_name' => 'KTP Eko Prasetyo',
            'file_path' => 'documents/technicians/tech1_ktp.pdf',
            'file_size' => 1024500,
            'mime_type' => 'application/pdf',
            'is_verified' => true,
            'notes' => 'NIK dan identitas valid.',
        ]);

        TechnicianDocument::create([
            'technician_profile_id' => $techProfile1->id,
            'document_type' => 'certificate',
            'document_name' => 'Sertifikat MikroTik MTCNA',
            'file_path' => 'documents/technicians/tech1_mtcna.pdf',
            'file_size' => 2048100,
            'mime_type' => 'application/pdf',
            'is_verified' => true,
            'notes' => 'Sertifikasi MTCNA aktif hingga 2027.',
        ]);

        $techProfile2 = TechnicianProfile::create([
            'user_id' => $userTech2->id,
            'technician_code' => 'TECH-2026-002',
            'phone' => '081987654321',
            'specialization' => 'Access Point Enterprise, Cabling Cat6/Cat7, Troubleshooting Latency & WiFi Drop',
            'bio' => 'Ahli tuning sinyal WiFi, eliminasi dead zone, dan perapian rack server.',
            'experience_years' => 3,
            'verification_status' => 'verified',
            'verified_at' => now()->subMonths(2),
            'verified_by' => $admin->id,
            'is_available' => true,
            'current_latitude' => -6.88500000,
            'current_longitude' => 107.61500000,
        ]);

        TechnicianDocument::create([
            'technician_profile_id' => $techProfile2->id,
            'document_type' => 'certificate',
            'document_name' => 'Sertifikat BNSP Teknisi Jaringan Madya',
            'file_path' => 'documents/technicians/tech2_bnsp.pdf',
            'file_size' => 1536000,
            'mime_type' => 'application/pdf',
            'is_verified' => true,
            'notes' => 'Kompetensi teruji secara nasional.',
        ]);

        // =========================================================================
        // 5. SERVICE CATEGORIES & SERVICES & HISTORICAL PRICES
        // =========================================================================
        $catTroubleshoot = ServiceCategory::create([
            'name' => 'Troubleshooting & Analisa Sinyal',
            'slug' => 'troubleshooting',
            'description' => 'Pemeriksaan mendalam dan perbaikan kendala koneksi drop, sinyal hilang, atau router error.',
            'icon' => 'fa-wrench',
            'is_active' => true,
        ]);

        $catInstalasi = ServiceCategory::create([
            'name' => 'Instalasi & Perluasan Jaringan',
            'slug' => 'instalasi',
            'description' => 'Pemasangan Access Point baru, penarikan kabel LAN UTP, dan instalasi perangkat WiFi Mesh.',
            'icon' => 'fa-network-wired',
            'is_active' => true,
        ]);

        $catOptimasi = ServiceCategory::create([
            'name' => 'Optimasi & Manajemen Bandwidth',
            'slug' => 'optimasi',
            'description' => 'Tuning channel frekuensi anti-interferensi dan alokasi prioritas bandwidth.',
            'icon' => 'fa-tachometer-alt',
            'is_active' => true,
        ]);

        $catMaintenance = ServiceCategory::create([
            'name' => 'Perawatan Berkala & Keamanan',
            'slug' => 'maintenance',
            'description' => 'Pembersihan perangkat keras, update firmware, dan pengamanan enkripsi WPA3.',
            'icon' => 'fa-shield-alt',
            'is_active' => true,
        ]);

        // Service 1: Diagnosa & Perbaikan WiFi Drop
        $svcDiag = Service::create([
            'service_category_id' => $catTroubleshoot->id,
            'code' => 'SVC-WIFI-DIAG',
            'name' => 'Diagnosa & Perbaikan WiFi Drop / Loss Sinyal',
            'slug' => 'diagnosa-perbaikan-wifi-drop',
            'description' => 'Audit kualitas fisik sinyal, deteksi redaman fiber/kabel, dan re-terminasi konektor.',
            'estimated_duration_minutes' => 60,
            'is_active' => true,
        ]);

        ServicePrice::create([
            'service_id' => $svcDiag->id,
            'price' => 120000.00,
            'effective_from' => '2026-01-01 00:00:00',
            'effective_until' => null,
            'is_active' => true,
            'notes' => 'Tarif standar 2026',
        ]);

        // Service 2: Reset & Konfigurasi Ulang Router
        $svcRouter = Service::create([
            'service_category_id' => $catTroubleshoot->id,
            'code' => 'SVC-ROUTER-CFG',
            'name' => 'Reset & Rekonfigurasi Ulang Router / Modem ISP',
            'slug' => 'reset-konfigurasi-ulang-router',
            'description' => 'Setup PPPoE, DHCP Pool, DNS Caching Cloudflare/Google, dan isolasi Client.',
            'estimated_duration_minutes' => 45,
            'is_active' => true,
        ]);

        ServicePrice::create([
            'service_id' => $svcRouter->id,
            'price' => 85000.00,
            'effective_from' => '2026-01-01 00:00:00',
            'effective_until' => null,
            'is_active' => true,
            'notes' => 'Tarif standar 2026',
        ]);

        // Service 3: Pemasangan WiFi Mesh Node
        $svcMesh = Service::create([
            'service_category_id' => $catInstalasi->id,
            'code' => 'SVC-MESH-INST',
            'name' => 'Pemasangan & Setup Unit WiFi Mesh Node Tambahan',
            'slug' => 'pemasangan-setup-wifi-mesh',
            'description' => 'Pemasangan perangkat seamless roaming untuk menghilangkan area blank spot di rumah bertingkat.',
            'estimated_duration_minutes' => 90,
            'is_active' => true,
        ]);

        ServicePrice::create([
            'service_id' => $svcMesh->id,
            'price' => 175000.00,
            'effective_from' => '2026-01-01 00:00:00',
            'effective_until' => null,
            'is_active' => true,
            'notes' => 'Tarif standar 2026',
        ]);

        // Service 4: Optimasi Kanal WiFi
        $svcOpt = Service::create([
            'service_category_id' => $catOptimasi->id,
            'code' => 'SVC-WIFI-OPT',
            'name' => 'Optimasi Spektrum Kanal Frekuensi (2.4GHz & 5GHz Band)',
            'slug' => 'optimasi-spektrum-kanal-frekuensi',
            'description' => 'Scanning interferensi sinyal WiFi sekitar menggunakan spectrum analyzer dan penyesuaian channel.',
            'estimated_duration_minutes' => 60,
            'is_active' => true,
        ]);

        ServicePrice::create([
            'service_id' => $svcOpt->id,
            'price' => 95000.00,
            'effective_from' => '2026-01-01 00:00:00',
            'effective_until' => null,
            'is_active' => true,
            'notes' => 'Tarif standar 2026',
        ]);

        // =========================================================================
        // 6. ISSUE CATEGORIES & CUSTOMER ISSUES
        // =========================================================================
        $issueCatDrop = IssueCategory::create([
            'name' => 'Koneksi Sering Putus / Disconnect Berkala',
            'slug' => 'koneksi-sering-putus',
            'description' => 'Sinyal WiFi terhubung namun internet mendadak hilang atau reload setiap beberapa menit.',
        ]);

        $issueCatSlow = IssueCategory::create([
            'name' => 'Kecepatan Internet Lambat (Bandwidth Drop)',
            'slug' => 'kecepatan-internet-lambat',
            'description' => 'Hasil speed test jauh di bawah paket langganan ISP.',
        ]);

        $issueCatDeadZone = IssueCategory::create([
            'name' => 'Titik Mati / Sinyal Tidak Menjangkau Area Tertentu',
            'slug' => 'dead-zone-sinyal-lemah',
            'description' => 'Ruangan tertentu di rumah atau kantor tidak terjangkau jangkauan sinyal router.',
        ]);

        // Customer Issue Andi
        $issueAndi = CustomerIssue::create([
            'user_id' => $customer1->id,
            'issue_category_id' => $issueCatDrop->id,
            'title' => 'WiFi lantai 2 sering putus saat meeting Zoom',
            'description' => 'Ketika berada di kamar kerja lantai 2, indikator WiFi sering drop ke 1 bar dan sambungan terputus tiba-tiba, padahal di lantai 1 dekat router internet sangat lancar.',
            'initial_diagnosis' => 'Pelemahan daya sinyal radio karena pantulan dinding beton bertulang dan potensi tabrakan frekuensi saluran 6 dengan WiFi tetangga.',
        ]);

        // =========================================================================
        // 7. REALISTIC ORDER 1 (Completed, Paid, Rated)
        // =========================================================================
        $order1 = Order::create([
            'order_number' => 'ORD-20260901-0001',
            'customer_id' => $customer1->id,
            'technician_id' => $techProfile1->id,
            'address_id' => $addressAndiHome->id,
            'address_snapshot' => [
                'recipient_name' => $addressAndiHome->recipient_name,
                'phone' => $addressAndiHome->phone,
                'address_line' => $addressAndiHome->address_line,
                'village' => $addressAndiHome->village,
                'district' => $addressAndiHome->district,
                'city' => $addressAndiHome->city,
                'province' => $addressAndiHome->province,
                'postal_code' => $addressAndiHome->postal_code,
                'benchmark_notes' => $addressAndiHome->benchmark_notes,
                'latitude' => $addressAndiHome->latitude,
                'longitude' => $addressAndiHome->longitude,
            ],
            'customer_issue_id' => $issueAndi->id,
            'scheduled_at' => '2026-09-02 10:00:00',
            'status' => Order::STATUS_COMPLETED,
            'subtotal' => 215000.00,
            'service_fee' => 15000.00,
            'discount' => 0.00,
            'total_amount' => 230000.00,
            'notes' => 'Mohon datang tepat waktu karena jam 13:00 ada jadwal video conference penting.',
            'confirmed_at' => '2026-09-01 15:30:00',
            'accepted_at' => '2026-09-01 16:00:00',
            'started_at' => '2026-09-02 10:05:00',
            'completed_at' => '2026-09-02 11:40:00',
        ]);

        // Items for Order 1 (Snapshot Harga Master Saat Transaksi)
        OrderItem::create([
            'order_id' => $order1->id,
            'service_id' => $svcDiag->id,
            'quantity' => 1,
            'unit_price' => 120000.00,
            'subtotal' => 120000.00,
            'notes' => 'Pengecekan redaman kabel LAN dan sinyal lantai 2',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'service_id' => $svcOpt->id,
            'quantity' => 1,
            'unit_price' => 95000.00,
            'subtotal' => 95000.00,
            'notes' => 'Tuning frekuensi kanal 2.4GHz & 5GHz',
        ]);

        // Status History Order 1 (>= 5 steps)
        $statusFlow = [
            ['status' => 'pending', 'time' => '2026-09-01 14:15:00', 'by' => $customer1->id, 'note' => 'Pesanan baru dibuat oleh pelanggan.'],
            ['status' => 'confirmed', 'time' => '2026-09-01 15:30:00', 'by' => $admin->id, 'note' => 'Pesanan diverifikasi dan disetujui oleh admin.'],
            ['status' => 'assigned', 'time' => '2026-09-01 15:45:00', 'by' => $admin->id, 'note' => 'Admin mendelegasikan tugas kepada teknisi Eko Prasetyo.'],
            ['status' => 'accepted', 'time' => '2026-09-01 16:00:00', 'by' => $userTech1->id, 'note' => 'Teknisi menyetujui penugasan dan siap meluncur sesuai jadwal.'],
            ['status' => 'on_the_way', 'time' => '2026-09-02 09:30:00', 'by' => $userTech1->id, 'note' => 'Teknisi berangkat menuju alamat pelanggan.'],
            ['status' => 'in_progress', 'time' => '2026-09-02 10:05:00', 'by' => $userTech1->id, 'note' => 'Teknisi telah tiba di lokasi dan mulai melakukan diagnosa awal.'],
            ['status' => 'repairing', 'time' => '2026-09-02 10:40:00', 'by' => $userTech1->id, 'note' => 'Melakukan tuning kanal dan re-orientasi antena perangkat.'],
            ['status' => 'completed', 'time' => '2026-09-02 11:40:00', 'by' => $userTech1->id, 'note' => 'Pekerjaan perbaikan selesai dan telah diuji bersama pelanggan.'],
        ];

        foreach ($statusFlow as $step) {
            OrderStatusHistory::create([
                'order_id' => $order1->id,
                'status' => $step['status'],
                'changed_by' => $step['by'],
                'notes' => $step['note'],
                'created_at' => $step['time'],
                'updated_at' => $step['time'],
            ]);
        }

        // Assignment History Order 1
        TechnicianAssignment::create([
            'order_id' => $order1->id,
            'technician_id' => $techProfile1->id,
            'assigned_by' => $admin->id,
            'assigned_at' => '2026-09-01 15:45:00',
            'accepted_at' => '2026-09-01 16:00:00',
        ]);

        // Field Diagnosis (1 diagnosis)
        Diagnosis::create([
            'order_id' => $order1->id,
            'technician_id' => $techProfile1->id,
            'problem_found' => 'Ditemukan pelemahan sinyal signifikan pada lantai 2 (RSSI -82 dBm) karena antena router lantai 1 terhalang lemari buku dan kanal WiFi 2.4GHz bertumpuk pada Channel 6 dengan 4 jaringan tetangga.',
            'diagnosis_result' => 'Level noise interferensi tinggi (-78 dBm) sehingga paket data sering retransmit dan menyebabkan freeze saat video call.',
            'network_condition' => 'RSSI Awal: -82 dBm, Ping ke Gateway: 45ms, Packet Loss: 8%, Speedtest: Download 6.2 Mbps / Upload 1.1 Mbps.',
            'recommendation' => 'Pindahkan posisi router ke area terbuka serta pisahkan SSID 2.4GHz dan 5GHz dengan penetapan kanal frekuensi Channel 1 dan Channel 36.',
            'diagnosed_at' => '2026-09-02 10:35:00',
        ]);

        // Final Work Report
        $report1 = WorkReport::create([
            'order_id' => $order1->id,
            'technician_id' => $techProfile1->id,
            'diagnosis_summary' => 'Interferensi kanal frekuensi 2.4GHz padat dan pelemahan sinyal fisik akibat posisi router terhalang perabotan.',
            'action_taken' => '1. Memindahkan letak router ke rak dinding terbuka ketinggian 1.8 meter. 2. Mengubah pengaturan WiFi kanal 2.4GHz dari Channel 6 ke Channel 1 (Lebar pita 20MHz). 3. Mengaktifkan pita 5GHz pada Channel 36 untuk perangkat laptop kerja.',
            'result' => 'Sinyal di lantai 2 naik drastis ke RSSI -58 dBm (Sangat Baik). Pengujian Speedtest mencapai Download 48.5 Mbps / Upload 18.2 Mbps dengan Latency 9ms dan 0% Packet Loss.',
            'technician_notes' => 'Laptop kerja pelanggan telah dihubungkan langsung ke SSID 5GHz. Diberikan garansi pengerjaan selama 7 hari kalender.',
            'started_at' => '2026-09-02 10:05:00',
            'completed_at' => '2026-09-02 11:40:00',
        ]);

        // Work Report Attachments (3 Bukti Foto Lapangan)
        WorkReportAttachment::create([
            'work_report_id' => $report1->id,
            'file_path' => 'reports/ORD-20260901-0001/before_spectrum.jpg',
            'file_name' => 'spectrum_analysis_before.jpg',
            'file_size' => 450200,
            'mime_type' => 'image/jpeg',
            'attachment_type' => 'before',
            'uploaded_by' => $userTech1->id,
        ]);

        WorkReportAttachment::create([
            'work_report_id' => $report1->id,
            'file_path' => 'reports/ORD-20260901-0001/in_progress_relocation.jpg',
            'file_name' => 'router_relocation_process.jpg',
            'file_size' => 520400,
            'mime_type' => 'image/jpeg',
            'attachment_type' => 'in_progress',
            'uploaded_by' => $userTech1->id,
        ]);

        WorkReportAttachment::create([
            'work_report_id' => $report1->id,
            'file_path' => 'reports/ORD-20260901-0001/after_speedtest.jpg',
            'file_name' => 'speedtest_result_after.jpg',
            'file_size' => 380100,
            'mime_type' => 'image/jpeg',
            'attachment_type' => 'speedtest',
            'uploaded_by' => $userTech1->id,
        ]);

        // Payment Order 1
        Payment::create([
            'order_id' => $order1->id,
            'payment_reference' => 'PAY-20260902-0001',
            'amount' => 230000.00,
            'payment_method' => 'qris',
            'status' => Payment::STATUS_PAID,
            'gateway_provider' => 'Midtrans',
            'gateway_response' => [
                'transaction_id' => 'mid-trx-987654321',
                'payment_type' => 'qris',
                'issuer' => 'GoPay',
                'status_code' => '200',
                'settlement_time' => '2026-09-02 11:45:10',
            ],
            'paid_at' => '2026-09-02 11:45:10',
            'expired_at' => '2026-09-02 13:45:00',
        ]);

        // Rating Order 1
        Rating::create([
            'order_id' => $order1->id,
            'customer_id' => $customer1->id,
            'technician_id' => $techProfile1->id,
            'rating' => 5,
            'score' => 5,
            'review' => 'Pelayanan Mas Eko sangat profesional! Menjelaskan detail penyebab sinyal drop tanpa menggurui dan solusinya langsung terasa nyata. Sekarang meeting Zoom di lantai 2 lancar tanpa jeda. Rekomendasi banget!',
            'response_from_technician' => 'Terima kasih banyak atas ulasan positifnya, Pak Andi. Senang bisa membantu kelancaran kerja dari rumah Bapak.',
            'is_public' => true,
        ]);

        // =========================================================================
        // 8. REALISTIC ORDER 2 (In Progress / Sedang Dikerjakan)
        // =========================================================================
        $order2 = Order::create([
            'order_number' => 'ORD-20260907-0002',
            'customer_id' => $customer2->id,
            'technician_id' => $techProfile2->id,
            'address_id' => $addressSitiKos->id,
            'address_snapshot' => [
                'recipient_name' => $addressSitiKos->recipient_name,
                'phone' => $addressSitiKos->phone,
                'address_line' => $addressSitiKos->address_line,
                'village' => $addressSitiKos->village,
                'district' => $addressSitiKos->district,
                'city' => $addressSitiKos->city,
                'province' => $addressSitiKos->province,
                'postal_code' => $addressSitiKos->postal_code,
                'benchmark_notes' => $addressSitiKos->benchmark_notes,
                'latitude' => $addressSitiKos->latitude,
                'longitude' => $addressSitiKos->longitude,
            ],
            'scheduled_at' => '2026-09-07 14:00:00',
            'status' => Order::STATUS_IN_PROGRESS,
            'subtotal' => 175000.00,
            'service_fee' => 15000.00,
            'discount' => 0.00,
            'total_amount' => 190000.00,
            'notes' => 'Kamar kos di pojok lantai 2, kunci gerbang ada di ibu kos.',
            'confirmed_at' => '2026-09-07 10:00:00',
            'accepted_at' => '2026-09-07 10:30:00',
            'started_at' => '2026-09-07 14:10:00',
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'service_id' => $svcMesh->id,
            'quantity' => 1,
            'unit_price' => 175000.00,
            'subtotal' => 175000.00,
            'notes' => 'Pemasangan wireless mesh node TP-Link Deco di koridor lantai 2',
        ]);

        $order2Steps = [
            ['status' => 'pending', 'time' => '2026-09-07 09:15:00', 'by' => $customer2->id, 'note' => 'Booking instalasi WiFi Mesh dibuat.'],
            ['status' => 'confirmed', 'time' => '2026-09-07 10:00:00', 'by' => $admin->id, 'note' => 'Order dikonfirmasi oleh Admin.'],
            ['status' => 'assigned', 'time' => '2026-09-07 10:15:00', 'by' => $admin->id, 'note' => 'Penugasan teknisi Budi Santoso.'],
            ['status' => 'accepted', 'time' => '2026-09-07 10:30:00', 'by' => $userTech2->id, 'note' => 'Teknisi menyetujui jadwal penugasan.'],
            ['status' => 'on_the_way', 'time' => '2026-09-07 13:30:00', 'by' => $userTech2->id, 'note' => 'Teknisi berangkat menuju kos customer.'],
            ['status' => 'in_progress', 'time' => '2026-09-07 14:10:00', 'by' => $userTech2->id, 'note' => 'Teknisi tiba dan sedang melakukan instalasi perangkat.'],
        ];

        foreach ($order2Steps as $step) {
            OrderStatusHistory::create([
                'order_id' => $order2->id,
                'status' => $step['status'],
                'changed_by' => $step['by'],
                'notes' => $step['note'],
                'created_at' => $step['time'],
                'updated_at' => $step['time'],
            ]);
        }

        TechnicianAssignment::create([
            'order_id' => $order2->id,
            'technician_id' => $techProfile2->id,
            'assigned_by' => $admin->id,
            'assigned_at' => '2026-09-07 10:15:00',
            'accepted_at' => '2026-09-07 10:30:00',
        ]);

        // Payment Order 2 (Menunggu Konfirmasi Transfer Manual)
        $pay2 = Payment::create([
            'order_id' => $order2->id,
            'payment_reference' => 'PAY-20260907-0002',
            'amount' => 190000.00,
            'payment_method' => 'bank_transfer',
            'status' => Payment::STATUS_WAITING_CONFIRMATION,
            'gateway_provider' => 'Manual Transfer BCA',
            'paid_at' => null,
            'expired_at' => '2026-09-07 23:59:59',
        ]);

        PaymentConfirmation::create([
            'payment_id' => $pay2->id,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder_name' => 'Siti Rahmawati',
            'transfer_amount' => 190000.00,
            'transfer_date' => '2026-09-07',
            'proof_file_path' => 'payments/proofs/siti_transfer_bca.jpg',
            'status' => 'pending',
            'notes' => 'Sudah ditransfer melalui m-BCA jam 11:20 WIB.',
        ]);

        // =========================================================================
        // 9. AUDIT LOGS (Keamanan & Jejak Aktivitas)
        // =========================================================================
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'login',
            'auditable_type' => User::class,
            'auditable_id' => $admin->id,
            'old_values' => null,
            'new_values' => ['ip' => '127.0.0.1', 'status' => 'success'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0',
            'created_at' => '2026-09-01 08:00:00',
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'verify_technician',
            'auditable_type' => TechnicianProfile::class,
            'auditable_id' => $techProfile1->id,
            'old_values' => ['verification_status' => 'pending'],
            'new_values' => ['verification_status' => 'verified', 'verified_by' => $admin->id],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0',
            'created_at' => '2026-09-01 08:30:00',
        ]);

        AuditLog::create([
            'user_id' => $customer1->id,
            'action' => 'create_order',
            'auditable_type' => Order::class,
            'auditable_id' => $order1->id,
            'old_values' => null,
            'new_values' => ['order_number' => 'ORD-20260901-0001', 'total_amount' => 230000.00],
            'ip_address' => '180.252.112.44',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X)',
            'created_at' => '2026-09-01 14:15:00',
        ]);
    }
}
