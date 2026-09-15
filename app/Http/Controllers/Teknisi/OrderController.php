<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Diagnosis;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\WorkReport;
use App\Models\WorkReportAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Mendapatkan technician profile ID milik user yang login.
     * Juga memastikan hanya order milik teknisi ini yang dapat diakses.
     */
    private function getTechId(): int
    {
        $profile = auth()->user()->technicianProfile;
        if (! $profile) {
            abort(403, 'Profil teknisi tidak ditemukan.');
        }
        return $profile->id;
    }

    private function findOwnOrder(int $orderId): Order
    {
        return Order::where('id', $orderId)
            ->where('technician_id', $this->getTechId())
            ->firstOrFail();
    }

    /**
     * Daftar pekerjaan aktif teknisi.
     */
    public function index(Request $request): View
    {
        $techId = $this->getTechId();

        $orders = Order::where('technician_id', $techId)
            ->whereNotIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->with(['customer:id,name,phone', 'items.service:id,name'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderBy('scheduled_at')
            ->paginate(15)
            ->withQueryString();

        return view('teknisi.orders.index', compact('orders'));
    }

    /**
     * Detail pekerjaan (hanya milik sendiri).
     */
    public function show(int $id): View
    {
        $order = Order::where('id', $id)
            ->where('technician_id', $this->getTechId())
            ->with([
                'customer:id,name,email,phone',
                'address',
                'customerIssue.category',
                'items.service:id,name,code',
                'statusHistories.changer:id,name',
                'diagnoses',
                'workReport.attachments',
            ])
            ->firstOrFail();

        return view('teknisi.orders.show', compact('order'));
    }

    /**
     * Teknisi menerima penugasan.
     */
    public function accept(int $id): RedirectResponse
    {
        $order = $this->findOwnOrder($id);

        if ($order->status !== Order::STATUS_ASSIGNED) {
            return back()->with('error', 'Pekerjaan tidak dalam status yang dapat diterima.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status'      => Order::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => Order::STATUS_ACCEPTED,
                'changed_by' => auth()->id(),
                'notes'      => 'Penugasan diterima oleh teknisi.',
            ]);

            // Update assignment record
            $order->assignments()
                ->where('technician_id', $this->getTechId())
                ->whereNull('accepted_at')
                ->latest('assigned_at')
                ->first()
                ?->update(['accepted_at' => now()]);
        });

        return back()->with('success', 'Penugasan berhasil diterima.');
    }

    /**
     * Update status pekerjaan secara progresif.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $order = $this->findOwnOrder($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Order::STATUS_ON_THE_WAY,
                Order::STATUS_IN_PROGRESS,
                Order::STATUS_DIAGNOSIS,
                Order::STATUS_REPAIRING,
            ])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Validasi transisi status yang logis
        $allowedTransitions = [
            Order::STATUS_ACCEPTED     => [Order::STATUS_ON_THE_WAY],
            Order::STATUS_ON_THE_WAY   => [Order::STATUS_IN_PROGRESS],
            Order::STATUS_IN_PROGRESS  => [Order::STATUS_DIAGNOSIS, Order::STATUS_REPAIRING],
            Order::STATUS_DIAGNOSIS    => [Order::STATUS_REPAIRING],
            Order::STATUS_REPAIRING    => [],
        ];

        $allowed = $allowedTransitions[$order->status] ?? [];
        if (! in_array($validated['status'], $allowed)) {
            return back()->with('error', 'Transisi status tidak valid dari "' . $order->status . '" ke "' . $validated['status'] . '".');
        }

        DB::transaction(function () use ($order, $validated) {
            $updateData = ['status' => $validated['status']];
            if ($validated['status'] === Order::STATUS_IN_PROGRESS && ! $order->started_at) {
                $updateData['started_at'] = now();
            }

            $order->update($updateData);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => $validated['status'],
                'changed_by' => auth()->id(),
                'notes'      => $validated['notes'] ?? null,
            ]);
        });

        $statusLabels = [
            'on_the_way'  => 'Dalam Perjalanan',
            'in_progress' => 'Sedang Dikerjakan',
            'diagnosis'   => 'Proses Diagnosa',
            'repairing'   => 'Proses Perbaikan',
        ];

        return back()->with('success', 'Status diperbarui: ' . ($statusLabels[$validated['status']] ?? $validated['status']));
    }

    /**
     * Form laporan pekerjaan dan upload bukti.
     */
    public function createReport(int $id): View
    {
        $order = Order::where('id', $id)
            ->where('technician_id', $this->getTechId())
            ->with(['customer:id,name', 'items.service:id,name', 'workReport.attachments'])
            ->firstOrFail();

        if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_ASSIGNED, Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])) {
            return redirect()->route('teknisi.orders.show', $order->id)
                ->with('error', 'Pekerjaan belum dalam tahap yang memerlukan laporan.');
        }

        return view('teknisi.orders.report', compact('order'));
    }

    /**
     * Simpan laporan pekerjaan dan selesaikan order.
     */
    public function storeReport(Request $request, int $id): RedirectResponse
    {
        $order = $this->findOwnOrder($id);

        if (in_array($order->status, [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED, Order::STATUS_PENDING])) {
            return back()->with('error', 'Pekerjaan tidak dalam status yang dapat dilaporkan.');
        }

        $validated = $request->validate([
            'diagnosis_summary' => ['required', 'string', 'max:2000'],
            'action_taken'      => ['required', 'string', 'max:2000'],
            'result'            => ['required', 'string', 'max:2000'],
            'technician_notes'  => ['nullable', 'string', 'max:1000'],
            'photos'            => ['required', 'array', 'min:1', 'max:10'],
            'photos.*'          => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'attachment_types'  => ['nullable', 'array'],
            'attachment_types.*'=> ['nullable', Rule::in(['before', 'in_progress', 'after', 'signature', 'speedtest', 'other'])],
        ], [
            'diagnosis_summary.required' => 'Ringkasan diagnosa wajib diisi.',
            'action_taken.required'      => 'Tindakan yang dilakukan wajib diisi.',
            'result.required'            => 'Hasil pekerjaan wajib diisi.',
            'photos.required'            => 'Minimal satu foto bukti wajib diunggah.',
            'photos.min'                 => 'Minimal satu foto bukti wajib diunggah.',
            'photos.*.image'             => 'File harus berupa gambar.',
            'photos.*.mimes'             => 'Format foto harus JPEG, PNG, atau WebP.',
            'photos.*.max'               => 'Ukuran foto maksimal 5MB.',
        ]);

        $techId = $this->getTechId();

        DB::transaction(function () use ($order, $validated, $techId) {
            // Create or update work report
            $report = WorkReport::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'technician_id'    => $techId,
                    'diagnosis_summary'=> $validated['diagnosis_summary'],
                    'action_taken'     => $validated['action_taken'],
                    'result'           => $validated['result'],
                    'technician_notes' => $validated['technician_notes'] ?? null,
                    'started_at'       => $order->started_at ?? now(),
                    'completed_at'     => now(),
                ]
            );

            // Upload photos
            foreach ($validated['photos'] as $index => $photo) {
                $extension = $photo->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;
                $path = $photo->storeAs(
                    'reports/' . $order->order_number,
                    $filename,
                    'public'
                );

                WorkReportAttachment::create([
                    'work_report_id'  => $report->id,
                    'file_path'       => $path,
                    'file_name'       => $photo->getClientOriginalName(),
                    'file_size'       => $photo->getSize(),
                    'mime_type'       => $photo->getMimeType(),
                    'attachment_type' => $validated['attachment_types'][$index] ?? 'after',
                    'uploaded_by'     => auth()->id(),
                ]);
            }

            // Complete the order
            $order->update([
                'status'       => Order::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => Order::STATUS_COMPLETED,
                'changed_by' => auth()->id(),
                'notes'      => 'Pekerjaan selesai. Laporan dan bukti foto telah diunggah.',
            ]);

            AuditLog::record('complete_order', $order);
        });

        return redirect()->route('teknisi.orders.show', $order->id)
            ->with('success', 'Laporan pekerjaan berhasil disimpan dan pekerjaan telah diselesaikan.');
    }

    /**
     * Riwayat pekerjaan teknisi (yang sudah selesai/dibatalkan).
     */
    public function history(Request $request): View
    {
        $techId = $this->getTechId();

        $orders = Order::where('technician_id', $techId)
            ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->with(['customer:id,name', 'items.service:id,name', 'workReport'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('completed_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('completed_at', '<=', $request->date_to))
            ->latest('completed_at')
            ->paginate(15)
            ->withQueryString();

        return view('teknisi.orders.history', compact('orders'));
    }
}
