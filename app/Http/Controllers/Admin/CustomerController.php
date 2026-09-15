<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AuditLog;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customerRoleId = Role::where('name', Role::CUSTOMER)->value('id');

        $customers = User::where('role_id', $customerRoleId)
            ->with(['customerProfile', 'addresses' => fn($q) => $q->where('is_default', true)])
            ->withCount('ordersAsCustomer')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('is_active', $request->status === 'active');
            })
            ->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'))
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'email'          => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'nik'            => ['nullable', 'string', 'max:16'],
            'gender'         => ['nullable', Rule::in(['male', 'female', 'other'])],
            'birth_date'     => ['nullable', 'date', 'before:today'],
            'address_label'  => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['nullable', 'string', 'max:100'],
            'address_phone'  => ['nullable', 'string', 'max:20'],
            'address_line'   => ['nullable', 'string'],
            'province'       => ['nullable', 'string', 'max:100'],
            'city'           => ['nullable', 'string', 'max:100'],
            'district'       => ['nullable', 'string', 'max:100'],
            'village'        => ['nullable', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:10'],
        ], [
            'name.required'      => 'Nama pelanggan wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        DB::transaction(function () use ($validated) {
            $customerRoleId = Role::where('name', Role::CUSTOMER)->value('id');

            $user = User::create([
                'role_id'           => $customerRoleId,
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'phone'             => $validated['phone'] ?? null,
                'password'          => Hash::make($validated['password']),
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            CustomerProfile::create([
                'user_id'    => $user->id,
                'nik'        => $validated['nik'] ?? null,
                'gender'     => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
            ]);

            if (! empty($validated['address_line'])) {
                Address::create([
                    'user_id'        => $user->id,
                    'label'          => $validated['address_label'] ?? 'Rumah',
                    'recipient_name' => $validated['recipient_name'] ?? $validated['name'],
                    'phone'          => $validated['address_phone'] ?? $validated['phone'] ?? '',
                    'address_line'   => $validated['address_line'],
                    'province'       => $validated['province'] ?? '',
                    'city'           => $validated['city'] ?? '',
                    'district'       => $validated['district'] ?? '',
                    'village'        => $validated['village'] ?? null,
                    'postal_code'    => $validated['postal_code'] ?? '',
                    'is_default'     => true,
                ]);
            }

            AuditLog::record('create_customer', $user);
        });

        return redirect()->route('admin.customers.index')
            ->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function show(int $id): View
    {
        $customer = User::where('id', $id)
            ->whereHas('role', fn($q) => $q->where('name', Role::CUSTOMER))
            ->with([
                'customerProfile',
                'addresses',
                'ordersAsCustomer' => fn($q) => $q->with(['technician.user:id,name'])->latest()->limit(10),
                'customerIssues'   => fn($q) => $q->with('category')->latest()->limit(10),
            ])
            ->withCount('ordersAsCustomer')
            ->firstOrFail();

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(int $id): View
    {
        $customer = User::where('id', $id)
            ->whereHas('role', fn($q) => $q->where('name', Role::CUSTOMER))
            ->with(['customerProfile', 'addresses' => fn($q) => $q->where('is_default', true)])
            ->firstOrFail();

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $customer = User::where('id', $id)
            ->whereHas('role', fn($q) => $q->where('name', Role::CUSTOMER))
            ->firstOrFail();

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'email'      => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($customer->id)],
            'phone'      => ['nullable', 'string', 'max:20'],
            'is_active'  => ['sometimes', 'boolean'],
            'nik'        => ['nullable', 'string', 'max:16'],
            'gender'     => ['nullable', Rule::in(['male', 'female', 'other'])],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ], [
            'name.required'  => 'Nama pelanggan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $oldValues = $customer->only(['name', 'email', 'phone', 'is_active']);

            $customer->update([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'] ?? null,
                'is_active' => $validated['is_active'] ?? $customer->is_active,
            ]);

            $customer->customerProfile()->updateOrCreate(
                ['user_id' => $customer->id],
                [
                    'nik'        => $validated['nik'] ?? null,
                    'gender'     => $validated['gender'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                ]
            );

            AuditLog::record('update_customer', $customer, $oldValues, $customer->fresh()->only(['name', 'email', 'phone', 'is_active']));
        });

        return redirect()->route('admin.customers.show', $customer->id)
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $customer = User::where('id', $id)
            ->whereHas('role', fn($q) => $q->where('name', Role::CUSTOMER))
            ->firstOrFail();

        // Soft delete
        AuditLog::record('delete_customer', $customer);
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
