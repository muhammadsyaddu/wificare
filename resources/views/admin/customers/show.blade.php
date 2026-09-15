<x-layouts.app :title="$customer->name" :header="'Detail Pelanggan'">
    <div class="mb-4"><a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali ke Daftar Pelanggan</a></div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Profil --}}
        <div class="card p-6 lg:col-span-1">
            <div class="flex items-center gap-4 mb-5">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-lg font-bold text-primary-700">{{ strtoupper(substr($customer->name, 0, 2)) }}</div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $customer->name }}</h2>
                    <span class="badge {{ $customer->is_active ? 'badge-completed' : 'badge-cancelled' }}">{{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-900">{{ $customer->email }}</dd></div>
                <div><dt class="text-gray-500">Telepon</dt><dd class="font-medium text-gray-900">{{ $customer->phone ?? '—' }}</dd></div>
                @if($customer->customerProfile)
                    <div><dt class="text-gray-500">NIK</dt><dd class="font-medium text-gray-900">{{ $customer->customerProfile->nik ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Jenis Kelamin</dt><dd class="font-medium text-gray-900">{{ $customer->customerProfile->gender === 'male' ? 'Laki-laki' : ($customer->customerProfile->gender === 'female' ? 'Perempuan' : ($customer->customerProfile->gender ?? '—')) }}</dd></div>
                    <div><dt class="text-gray-500">Tanggal Lahir</dt><dd class="font-medium text-gray-900">{{ $customer->customerProfile->birth_date?->translatedFormat('d F Y') ?? '—' }}</dd></div>
                @endif
                <div><dt class="text-gray-500">Bergabung</dt><dd class="font-medium text-gray-900">{{ $customer->created_at->translatedFormat('d F Y') }}</dd></div>
                <div><dt class="text-gray-500">Total Order</dt><dd class="font-medium text-gray-900">{{ $customer->orders_as_customer_count }}</dd></div>
            </dl>
            <div class="mt-5 pt-5 border-t border-gray-200 flex gap-2">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn-secondary btn-sm flex-1 justify-center">Edit</a>
                <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}" onsubmit="return confirm('Yakin ingin menghapus data pelanggan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm">Hapus</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            {{-- Alamat --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Daftar Alamat</h3>
                @forelse($customer->addresses as $addr)
                    <div class="py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-gray-900">{{ $addr->label }}</span>
                            @if($addr->is_default)<span class="badge badge-confirmed">Default</span>@endif
                        </div>
                        <p class="text-sm text-gray-600">{{ $addr->full_address }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4">Belum ada alamat terdaftar.</p>
                @endforelse
            </div>

            {{-- Riwayat Order --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Riwayat Pekerjaan</h3>
                @forelse($customer->ordersAsCustomer as $order)
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->scheduled_at?->translatedFormat('d M Y H:i') }} · {{ $order->technician?->user?->name ?? '—' }}</p>
                        </div>
                        @php $sl = ['pending'=>['Pending','badge-pending'],'confirmed'=>['Dikonfirmasi','badge-confirmed'],'assigned'=>['Ditugaskan','badge-assigned'],'accepted'=>['Diterima','badge-accepted'],'on_the_way'=>['Perjalanan','badge-on_the_way'],'in_progress'=>['Dikerjakan','badge-in_progress'],'diagnosis'=>['Diagnosa','badge-diagnosis'],'repairing'=>['Perbaikan','badge-repairing'],'completed'=>['Selesai','badge-completed'],'cancelled'=>['Dibatalkan','badge-cancelled']]; @endphp
                        <span class="badge {{ $sl[$order->status][1] ?? '' }}">{{ $sl[$order->status][0] ?? $order->status }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400 py-4">Belum ada riwayat pekerjaan.</p>
                @endforelse
            </div>

            {{-- Riwayat Gangguan --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Riwayat Gangguan</h3>
                @forelse($customer->customerIssues as $issue)
                    <div class="py-3 border-b border-gray-100 last:border-0">
                        <p class="text-sm font-medium text-gray-900">{{ $issue->title }}</p>
                        <p class="text-xs text-gray-500">{{ $issue->category?->name }} · {{ $issue->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4">Belum ada laporan gangguan.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
