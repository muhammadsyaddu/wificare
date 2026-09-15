<x-layouts.app :title="'Riwayat Pekerjaan'" :header="'Riwayat Pekerjaan'">
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="status" class="input-field sm:w-40" onchange="this.form.submit()">
            <option value="">Semua</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-field sm:w-40" title="Dari tanggal">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-field sm:w-40" title="Sampai tanggal">
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    @php $sl = ['completed'=>['Selesai','badge-completed'],'cancelled'=>['Dibatalkan','badge-cancelled']]; @endphp
    <div class="space-y-3">
        @forelse($orders as $order)
            <a href="{{ route('teknisi.orders.show', $order->id) }}" class="card p-4 block hover:ring-2 hover:ring-primary-200 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900">{{ $order->order_number }}</span>
                    <span class="badge {{ $sl[$order->status][1] ?? '' }}">{{ $sl[$order->status][0] ?? $order->status }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ $order->customer?->name }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500">
                    <span>{{ $order->completed_at?->translatedFormat('d M Y') ?? $order->updated_at->translatedFormat('d M Y') }}</span>
                    <span>{{ $order->items->pluck('service.name')->filter()->join(', ') }}</span>
                    @if($order->workReport)<span class="badge badge-completed !text-[10px]">Laporan Ada</span>@endif
                </div>
            </a>
        @empty
            <div class="py-12 text-center"><p class="text-sm text-gray-400">Belum ada riwayat pekerjaan.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-layouts.app>
