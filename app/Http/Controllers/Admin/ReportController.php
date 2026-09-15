<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TechnicianProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with([
            'customer:id,name',
            'technician.user:id,name',
            'items.service:id,name',
            'workReport',
        ])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('technician_id'), fn($q) => $q->where('technician_id', $request->technician_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('scheduled_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('scheduled_at', '<=', $request->date_to))
            ->when(
                ! $request->filled('status'),
                fn($q) => $q->where('status', 'completed')
            )
            ->latest('completed_at')
            ->paginate(20)
            ->withQueryString();

        $technicians = TechnicianProfile::verified()->with('user:id,name')->get();

        $summary = [
            'total'     => Order::where('status', 'completed')->count(),
            'month'     => Order::where('status', 'completed')->whereMonth('completed_at', now()->month)->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.reports.index', compact('orders', 'technicians', 'summary'));
    }
}
