<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function index(Request $request): View
    {
        $techRoleId = Role::where('name', Role::TECHNICIAN)->value('id');

        $technicians = User::where('role_id', $techRoleId)
            ->with(['technicianProfile'])
            ->withCount(['technicianProfile as orders_count' => function ($q) {
                // Count won't work through nested relation, so we'll handle in view
            }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhereHas('technicianProfile', fn($tp) =>
                              $tp->where('technician_code', 'like', "%{$search}%")
                                 ->orWhere('specialization', 'like', "%{$search}%")
                          );
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->whereHas('technicianProfile', fn($tp) =>
                    $tp->where('verification_status', $request->status)
                );
            })
            ->when($request->filled('availability'), function ($q) use ($request) {
                $q->whereHas('technicianProfile', fn($tp) =>
                    $tp->where('is_available', $request->availability === 'available')
                );
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.technicians.index', compact('technicians'));
    }

    public function show(int $id): View
    {
        $technician = User::where('id', $id)
            ->whereHas('role', fn($q) => $q->where('name', Role::TECHNICIAN))
            ->with([
                'technicianProfile.documents',
                'technicianProfile.ratings' => fn($q) => $q->with('customer:id,name')->latest()->limit(5),
                'technicianProfile.orders'  => fn($q) => $q->with('customer:id,name')->latest()->limit(10),
            ])
            ->firstOrFail();

        $profile = $technician->technicianProfile;
        $stats = [
            'total_orders'     => $profile ? $profile->orders()->count() : 0,
            'completed_orders' => $profile ? $profile->orders()->where('status', 'completed')->count() : 0,
            'active_orders'    => $profile ? $profile->orders()->whereNotIn('status', ['completed', 'cancelled'])->count() : 0,
            'avg_rating'       => $profile ? $profile->average_rating : 0,
        ];

        return view('admin.technicians.show', compact('technician', 'stats'));
    }

    public function toggleAvailability(int $id): RedirectResponse
    {
        $technician = User::where('id', $id)
            ->whereHas('role', fn($q) => $q->where('name', Role::TECHNICIAN))
            ->firstOrFail();

        $profile = $technician->technicianProfile;
        if ($profile) {
            $old = $profile->is_available;
            $profile->update(['is_available' => ! $old]);
            AuditLog::record('toggle_technician_availability', $profile, ['is_available' => $old], ['is_available' => ! $old]);
        }

        return back()->with('success', 'Status ketersediaan teknisi berhasil diperbarui.');
    }

    public function verify(Request $request, int $id): RedirectResponse
    {
        $technician = User::where('id', $id)
            ->whereHas('role', fn($q) => $q->where('name', Role::TECHNICIAN))
            ->firstOrFail();

        $profile = $technician->technicianProfile;
        if (! $profile) {
            return back()->with('error', 'Profil teknisi tidak ditemukan.');
        }

        $validated = $request->validate([
            'action'           => ['required', 'in:verify,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $profile->verification_status;

        if ($validated['action'] === 'verify') {
            $profile->update([
                'verification_status' => 'verified',
                'verified_at'         => now(),
                'verified_by'         => auth()->id(),
                'rejection_reason'    => null,
            ]);
            $message = 'Teknisi berhasil diverifikasi.';
        } else {
            $profile->update([
                'verification_status' => 'rejected',
                'rejection_reason'    => $validated['rejection_reason'],
            ]);
            $message = 'Verifikasi teknisi ditolak.';
        }

        AuditLog::record('verify_technician', $profile, ['verification_status' => $oldStatus], ['verification_status' => $profile->verification_status]);

        return back()->with('success', $message);
    }
}
