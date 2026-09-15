<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerIssue;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        // Statistik utama
        $totalCustomers = User::whereHas('role', fn($q) => $q->where('name', 'customer'))->count();
        $totalTechnicians = TechnicianProfile::verified()->count();

        $ordersPending    = Order::where('status', Order::STATUS_PENDING)->count();
        $ordersInProgress = Order::whereIn('status', [
            Order::STATUS_ASSIGNED, Order::STATUS_ACCEPTED,
            Order::STATUS_ON_THE_WAY, Order::STATUS_IN_PROGRESS,
            Order::STATUS_DIAGNOSIS, Order::STATUS_REPAIRING,
        ])->count();
        $ordersCompleted  = Order::where('status', Order::STATUS_COMPLETED)->count();
        $ordersCancelled  = Order::where('status', Order::STATUS_CANCELLED)->count();

        // Pekerjaan hari ini
        $ordersToday = Order::whereDate('scheduled_at', $today)
            ->with(['customer:id,name', 'technician.user:id,name'])
            ->latest('scheduled_at')
            ->limit(10)
            ->get();

        $ordersTodayCount = Order::whereDate('scheduled_at', $today)->count();

        // Pekerjaan terbaru (semua status)
        $recentOrders = Order::with(['customer:id,name', 'technician.user:id,name'])
            ->latest()
            ->limit(8)
            ->get();

        // Status distribution untuk chart
        $statusDistribution = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Aktivitas terbaru
        $recentActivities = OrderStatusHistory::with(['order:id,order_number', 'changer:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        // Gangguan terbaru
        $recentIssues = CustomerIssue::with(['user:id,name', 'category:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        // Pekerjaan per bulan (6 bulan terakhir) untuk chart
        $monthlyOrders = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyOrders[] = [
                'label' => $month->translatedFormat('M Y'),
                'total' => Order::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
                'completed' => Order::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->where('status', Order::STATUS_COMPLETED)
                    ->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalCustomers', 'totalTechnicians',
            'ordersPending', 'ordersInProgress', 'ordersCompleted', 'ordersCancelled',
            'ordersToday', 'ordersTodayCount',
            'recentOrders', 'statusDistribution',
            'recentActivities', 'recentIssues', 'monthlyOrders'
        ));
    }
}
