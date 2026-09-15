<x-layouts.app :title="'Tugas Saya'" :header="'Tugas Saya'">
    @php $sl = ['assigned'=>['Menunggu Konfirmasi','badge-assigned'],'accepted'=>['Diterima','badge-accepted'],'on_the_way'=>['Dalam Perjalanan','badge-on_the_way'],'in_progress'=>['Sedang Dikerjakan','badge-in_progress'],'diagnosis'=>['Proses Diagnosa','badge-diagnosis'],'repairing'=>['Proses Perbaikan','badge-repairing']]; @endphp

    <form method="GET" class="flex gap-3 mb-6">
        <select name="status" class="input-field sm:w-48" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach($sl as $k => $v)<option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $v[0] }}</option>@endforeach
        </select>
    </form>

    <div class="space-y-3">
        @forelse($orders as $order)
            <a href="{{ route('teknisi.orders.show', $order->id) }}" class="card p-4 block hover:ring-2 hover:ring-primary-200 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900">{{ $order->order_number }}</span>
                    <span class="badge {{ $sl[$order->status][1] ?? 'badge-pending' }}">{{ $sl[$order->status][0] ?? $order->status }}</span>
                </div>
                <p class="text-sm text-gray-700 mb-1">{{ $order->customer?->name }} · {{ $order->customer?->phone }}</p>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>{{ $order->scheduled_at?->translatedFormat('d M Y H:i') }}</span>
                    @if($order->items->count())<span>{{ $order->items->pluck('service.name')->filter()->join(', ') }}</span>@endif
                </div>
            </a>
        @empty
            <div class="py-12 text-center"><p class="text-sm text-gray-400 mb-3">Tidak ada tugas aktif saat ini.</p><a href="{{ route('teknisi.orders.history') }}" class="text-sm text-primary-600 font-medium">Lihat Riwayat →</a></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-layouts.app>
