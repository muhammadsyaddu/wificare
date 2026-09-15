<x-layouts.app :title="'Laporan'" :header="'Laporan Pekerjaan'">
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="card px-5 py-4"><p class="text-sm text-gray-500">Total Selesai</p><p class="text-2xl font-bold text-emerald-600">{{ $summary['total'] }}</p></div>
        <div class="card px-5 py-4"><p class="text-sm text-gray-500">Bulan Ini</p><p class="text-2xl font-bold text-primary-600">{{ $summary['month'] }}</p></div>
        <div class="card px-5 py-4"><p class="text-sm text-gray-500">Dibatalkan</p><p class="text-2xl font-bold text-red-500">{{ $summary['cancelled'] }}</p></div>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="technician_id" class="input-field sm:w-44" onchange="this.form.submit()">
            <option value="">Semua Teknisi</option>
            @foreach($technicians as $t)<option value="{{ $t->id }}" {{ request('technician_id') == $t->id ? 'selected' : '' }}>{{ $t->user?->name }}</option>@endforeach
        </select>
        <select name="status" class="input-field sm:w-36" onchange="this.form.submit()">
            <option value="">Selesai</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-field sm:w-40">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-field sm:w-40">
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    @php $sl = ['completed'=>['Selesai','badge-completed'],'cancelled'=>['Dibatalkan','badge-cancelled']]; @endphp
    <div class="table-container">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-gray-200 bg-gray-50/50"><tr>
                <th class="px-4 py-3 font-semibold text-gray-600">No. Order</th>
                <th class="px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">Pelanggan</th>
                <th class="px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">Teknisi</th>
                <th class="px-4 py-3 font-semibold text-gray-600 hidden lg:table-cell">Selesai</th>
                <th class="px-4 py-3 font-semibold text-gray-600 text-center hidden md:table-cell">Laporan</th>
                <th class="px-4 py-3 font-semibold text-gray-600 text-center">Status</th>
                <th class="px-4 py-3 font-semibold text-gray-600"></th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $order->customer?->name }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $order->technician?->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500 hidden lg:table-cell">{{ $order->completed_at?->translatedFormat('d M Y H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-center hidden md:table-cell">
                            @if($order->workReport)<span class="badge badge-completed">Ada</span>@else<span class="text-gray-400">—</span>@endif
                        </td>
                        <td class="px-4 py-3 text-center"><span class="badge {{ $sl[$order->status][1] ?? 'badge-pending' }}">{{ $sl[$order->status][0] ?? $order->status }}</span></td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary-600 text-sm font-medium">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada data laporan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-layouts.app>
