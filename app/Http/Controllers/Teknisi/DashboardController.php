<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\CustomerIssue;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard utama teknisi.
     *
     * Prinsip keamanan:
     * - Seluruh data pekerjaan dibatasi berdasarkan technician_id
     *   milik user yang sedang login.
     * - Tidak pernah mengambil data order teknisi lain.
     * - Tidak menggunakan ID teknisi dari request/query string.
     */
    public function index(): View
    {
        $user = auth()->user();

        $profile = $user->technicianProfile;

        if (! $profile) {
            abort(403, 'Profil teknisi tidak ditemukan.');
        }

        $techId = (int) $profile->id;

        $today = now()->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | 1. PEKERJAAN HARI INI
        |--------------------------------------------------------------------------
        */

        $todayOrders = Order::query()
            ->where('technician_id', $techId)
            ->whereDate('scheduled_at', $today->toDateString())
            ->whereNotIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])
            ->with([
                'customer:id,name,phone',
                'items.service:id,name',
            ])
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 2. PEKERJAAN AKTIF
        |--------------------------------------------------------------------------
        */

        $activeOrders = Order::query()
            ->where('technician_id', $techId)
            ->whereNotIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
                Order::STATUS_PENDING,
            ])
            ->with([
                'customer:id,name,phone',
                'items.service:id,name',
            ])
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 3. PEKERJAAN MENDATANG
        |--------------------------------------------------------------------------
        */

        $upcomingOrders = Order::query()
            ->where('technician_id', $techId)
            ->whereIn('status', [
                Order::STATUS_ASSIGNED,
                Order::STATUS_ACCEPTED,
            ])
            ->whereDate(
                'scheduled_at',
                '>',
                $today->toDateString()
            )
            ->with([
                'customer:id,name,phone',
                'items.service:id,name',
            ])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 4. STATISTIK TEKNISI
        |--------------------------------------------------------------------------
        */

        $totalCompleted = Order::query()
            ->where('technician_id', $techId)
            ->where('status', Order::STATUS_COMPLETED)
            ->count();

        $totalActive = Order::query()
            ->where('technician_id', $techId)
            ->whereNotIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])
            ->count();

        $monthCompleted = Order::query()
            ->where('technician_id', $techId)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereMonth('completed_at', $today->month)
            ->whereYear('completed_at', $today->year)
            ->count();

        $averageRating = (float) $profile->average_rating;


        /*
        |--------------------------------------------------------------------------
        | 5. JUMLAH PELANGGAN YANG PERNAH DITANGANI
        |--------------------------------------------------------------------------
        |
        | Ini berbeda dengan total pelanggan sistem.
        | Teknisi hanya melihat pelanggan yang memang pernah memiliki
        | pekerjaan yang ditugaskan kepadanya.
        |
        */

        $totalCustomers = Order::query()
            ->where('technician_id', $techId)
            ->whereNotNull('customer_id')
            ->distinct('customer_id')
            ->count('customer_id');


        /*
        |--------------------------------------------------------------------------
        | 6. DISTRIBUSI STATUS PEKERJAAN TEKNISI
        |--------------------------------------------------------------------------
        */

        $statusDistribution = Order::query()
            ->where('technician_id', $techId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($value) => (int) $value)
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | 7. PEKERJAAN 6 BULAN TERAKHIR
        |--------------------------------------------------------------------------
        |
        | Menggunakan satu query agregasi.
        | Tidak melakukan query berulang sebanyak 6 kali.
        |
        */

        $sixMonthsStart = $today
            ->copy()
            ->startOfMonth()
            ->subMonths(5);

        $monthlyRaw = Order::query()
            ->where('technician_id', $techId)
            ->where('created_at', '>=', $sixMonthsStart)
            ->selectRaw(
                'YEAR(created_at) as year,
                 MONTH(created_at) as month,
                 COUNT(*) as total'
            )
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(function ($row) {
                return sprintf(
                    '%04d-%02d',
                    $row->year,
                    $row->month
                );
            });


        $monthlyOrders = collect();

        for ($i = 5; $i >= 0; $i--) {

            $month = $today
                ->copy()
                ->startOfMonth()
                ->subMonths($i);

            $key = $month->format('Y-m');

            $monthlyOrders->push([
                'label' => $month->format('M Y'),
                'total' => (int) (
                    $monthlyRaw->get($key)->total
                    ?? 0
                ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 8. AKTIVITAS TERBARU TEKNISI
        |--------------------------------------------------------------------------
        |
        | Hanya histori order yang memang dimiliki teknisi login.
        |
        */

        $recentActivities = OrderStatusHistory::query()
            ->whereHas('order', function ($query) use ($techId) {
                $query->where('technician_id', $techId);
            })
            ->with([
                'order:id,order_number,technician_id',
                'changer:id,name',
            ])
            ->latest()
            ->limit(8)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 9. GANGGUAN TERBARU YANG BERKAITAN DENGAN PEKERJAAN TEKNISI
        |--------------------------------------------------------------------------
        */

        $recentIssues = CustomerIssue::query()
            ->whereHas('order', function ($query) use ($techId) {
                $query->where('technician_id', $techId);
            })
            ->with([
                'user:id,name',
                'category:id,name',
                'order:id,order_number,technician_id',
            ])
            ->latest()
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 10. STATUS LABEL
        |--------------------------------------------------------------------------
        |
        | Dikirim dari controller supaya Blade tidak perlu mengetahui
        | struktur bisnis terlalu banyak.
        |
        */

        $statusLabels = [
            Order::STATUS_PENDING => 'Pending',
            Order::STATUS_CONFIRMED => 'Dikonfirmasi',
            Order::STATUS_ASSIGNED => 'Ditugaskan',
            Order::STATUS_ACCEPTED => 'Diterima',
            Order::STATUS_ON_THE_WAY => 'Dalam Perjalanan',
            Order::STATUS_IN_PROGRESS => 'Dikerjakan',
            Order::STATUS_DIAGNOSIS => 'Diagnosa',
            Order::STATUS_REPAIRING => 'Perbaikan',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
        ];


        /*
        |--------------------------------------------------------------------------
        | 11. VIEW
        |--------------------------------------------------------------------------
        */

        return view('teknisi.dashboard', [
            'profile' => $profile,

            'todayOrders' => $todayOrders,
            'activeOrders' => $activeOrders,
            'upcomingOrders' => $upcomingOrders,

            'totalCustomers' => $totalCustomers,
            'totalCompleted' => $totalCompleted,
            'totalActive' => $totalActive,
            'monthCompleted' => $monthCompleted,
            'averageRating' => $averageRating,

            'statusDistribution' => $statusDistribution,
            'monthlyOrders' => $monthlyOrders,

            'recentActivities' => $recentActivities,
            'recentIssues' => $recentIssues,

            'statusLabels' => $statusLabels,
        ]);
    }
}