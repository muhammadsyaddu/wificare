<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AuditLog;
use App\Models\CustomerIssue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Role;
use App\Models\Service;
use App\Models\TechnicianAssignment;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['customer:id,name', 'technician.user:id,name', 'items.service:id,name'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                          ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('technician_id'), fn($q) => $q->where('technician_id', $request->technician_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('scheduled_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('scheduled_at', '<=', $request->date_to))
            ->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'))
            ->paginate(15)
            ->withQueryString();

        $technicians = TechnicianProfile::verified()->with('user:id,name')->get();
        $statuses = [
            'pending', 'confirmed', 'assigned', 'accepted', 'on_the_way',
            'in_progress', 'diagnosis', 'repairing', 'completed', 'cancelled',
        ];

        return view('admin.orders.index', compact('orders', 'technicians', 'statuses'));
    }

    public function create(Request $request): View
    {
        $customers = User::whereHas('role', fn($q) => $q->where('name', Role::CUSTOMER))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $services = Service::active()->with('activePrice')->orderBy('name')->get();
        $technicians = TechnicianProfile::available()->with('user:id,name')->get();

        // Jika dibuat dari customer issue
        $issue = null;
        if ($request->filled('issue_id')) {
            $issue = CustomerIssue::with('user:id,name')->find($request->issue_id);
        }

        return view('admin.orders.create', compact('customers', 'services', 'technicians', 'issue'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'      => ['required', 'exists:users,id'],
            'address_id'       => ['required', 'exists:addresses,id'],
            'scheduled_at'     => ['required', 'date', 'after:now'],
            'technician_id'    => ['nullable', 'exists:technician_profiles,id'],
            'customer_issue_id'=> ['nullable', 'exists:customer_issues,id'],
            'services'         => ['required', 'array', 'min:1'],
            'services.*.id'    => ['required', 'exists:services,id'],
            'services.*.qty'   => ['required', 'integer', 'min:1'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'service_fee'      => ['nullable', 'numeric', 'min:0'],
            'discount'         => ['nullable', 'numeric', 'min:0'],
        ], [
            'customer_id.required'  => 'Pelanggan wajib dipilih.',
            'address_id.required'   => 'Alamat wajib dipilih.',
            'scheduled_at.required' => 'Jadwal wajib diisi.',
            'scheduled_at.after'    => 'Jadwal harus di masa mendatang.',
            'services.required'     => 'Minimal satu layanan harus dipilih.',
        ]);

        // Verifikasi address milik customer
        $address = Address::where('id', $validated['address_id'])
            ->where('user_id', $validated['customer_id'])
            ->firstOrFail();

        DB::transaction(function () use ($validated, $address) {
            // Hitung subtotal
            $subtotal = 0;
            $orderItems = [];
            foreach ($validated['services'] as $svc) {
                $service = Service::with('activePrice')->findOrFail($svc['id']);
                $unitPrice = $service->activePrice?->price ?? 0;
                $qty = $svc['qty'];
                $itemSubtotal = $unitPrice * $qty;
                $subtotal += $itemSubtotal;
                $orderItems[] = [
                    'service_id' => $service->id,
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $itemSubtotal,
                ];
            }

            $serviceFee = $validated['service_fee'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $totalAmount = $subtotal + $serviceFee - $discount;

            // Generate order number
            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . Str::padLeft(
                Order::whereDate('created_at', now()->toDateString())->count() + 1, 4, '0'
            );

            $status = ! empty($validated['technician_id']) ? Order::STATUS_ASSIGNED : Order::STATUS_PENDING;

            $order = Order::create([
                'order_number'      => $orderNumber,
                'customer_id'       => $validated['customer_id'],
                'technician_id'     => $validated['technician_id'] ?? null,
                'address_id'        => $address->id,
                'address_snapshot'  => [
                    'recipient_name' => $address->recipient_name,
                    'phone'          => $address->phone,
                    'address_line'   => $address->address_line,
                    'village'        => $address->village,
                    'district'       => $address->district,
                    'city'           => $address->city,
                    'province'       => $address->province,
                    'postal_code'    => $address->postal_code,
                    'benchmark_notes'=> $address->benchmark_notes,
                    'latitude'       => $address->latitude,
                    'longitude'      => $address->longitude,
                ],
                'customer_issue_id' => $validated['customer_issue_id'] ?? null,
                'scheduled_at'      => $validated['scheduled_at'],
                'status'            => $status,
                'subtotal'          => $subtotal,
                'service_fee'       => $serviceFee,
                'discount'          => $discount,
                'total_amount'      => $totalAmount,
                'notes'             => $validated['notes'] ?? null,
                'confirmed_at'      => now(),
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }

            // Status history
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'pending',
                'changed_by' => auth()->id(),
                'notes'      => 'Pesanan dibuat oleh admin.',
            ]);

            if ($status === Order::STATUS_ASSIGNED) {
                OrderStatusHistory::create([
                    'order_id'   => $order->id,
                    'status'     => 'assigned',
                    'changed_by' => auth()->id(),
                    'notes'      => 'Teknisi ditugaskan saat pembuatan pesanan.',
                ]);

                TechnicianAssignment::create([
                    'order_id'     => $order->id,
                    'technician_id'=> $validated['technician_id'],
                    'assigned_by'  => auth()->id(),
                    'assigned_at'  => now(),
                ]);
            }

            AuditLog::record('create_order', $order);
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function show(int $id): View
    {
        $order = Order::with([
            'customer:id,name,email,phone',
            'technician.user:id,name',
            'address',
            'customerIssue.category',
            'items.service:id,name,code',
            'statusHistories.changer:id,name',
            'assignments.technician.user:id,name',
            'assignments.assigner:id,name',
            'diagnoses.technician.user:id,name',
            'workReport.attachments',
            'workReport.technician.user:id,name',
            'payments.confirmations',
            'rating.customer:id,name',
        ])->findOrFail($id);

        $availableTechnicians = TechnicianProfile::available()->with('user:id,name')->get();

        return view('admin.orders.show', compact('order', 'availableTechnicians'));
    }

    public function assign(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if (! in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED])) {
            return back()->with('error', 'Pesanan tidak dalam status yang memungkinkan penugasan teknisi.');
        }

        $validated = $request->validate([
            'technician_id' => ['required', 'exists:technician_profiles,id'],
        ], ['technician_id.required' => 'Teknisi wajib dipilih.']);

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'technician_id' => $validated['technician_id'],
                'status'        => Order::STATUS_ASSIGNED,
                'confirmed_at'  => $order->confirmed_at ?? now(),
            ]);

            TechnicianAssignment::create([
                'order_id'      => $order->id,
                'technician_id' => $validated['technician_id'],
                'assigned_by'   => auth()->id(),
                'assigned_at'   => now(),
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => Order::STATUS_ASSIGNED,
                'changed_by' => auth()->id(),
                'notes'      => 'Teknisi ditugaskan oleh admin.',
            ]);

            AuditLog::record('assign_technician', $order);
        });

        return back()->with('success', 'Teknisi berhasil ditugaskan.');
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status === Order::STATUS_COMPLETED || $order->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ], ['cancellation_reason.required' => 'Alasan pembatalan wajib diisi.']);

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'status'              => Order::STATUS_CANCELLED,
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_by'        => auth()->id(),
                'cancelled_at'        => now(),
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => Order::STATUS_CANCELLED,
                'changed_by' => auth()->id(),
                'notes'      => 'Dibatalkan: ' . $validated['cancellation_reason'],
            ]);

            AuditLog::record('cancel_order', $order);
        });

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
