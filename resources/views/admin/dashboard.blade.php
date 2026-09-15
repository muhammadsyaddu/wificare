<x-layouts.app :title="'Dashboard'" :header="'Dashboard Operasional'">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card px-5 py-4">
            <p class="text-sm font-medium text-gray-500">Total Pelanggan</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</p>
        </div>
        <div class="card px-5 py-4">
            <p class="text-sm font-medium text-gray-500">Total Teknisi</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalTechnicians) }}</p>
        </div>
        <div class="card px-5 py-4">
            <p class="text-sm font-medium text-gray-500">Pekerjaan Pending</p>
            <p class="mt-1 text-2xl font-bold text-amber-600">{{ number_format($ordersPending) }}</p>
        </div>
        <div class="card px-5 py-4">
            <p class="text-sm font-medium text-gray-500">Pekerjaan Selesai</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ number_format($ordersCompleted) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 mb-8">
        {{-- Status Distribution --}}
        <div class="card p-5 lg:col-span-1">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Status Pekerjaan</h3>
            <div class="space-y-3">
                @php
                    $statusLabels = [
                        'pending' => ['Pending', 'badge-pending'],
                        'confirmed' => ['Dikonfirmasi', 'badge-confirmed'],
                        'assigned' => ['Ditugaskan', 'badge-assigned'],
                        'accepted' => ['Diterima', 'badge-accepted'],
                        'on_the_way' => ['Dalam Perjalanan', 'badge-on_the_way'],
                        'in_progress' => ['Dikerjakan', 'badge-in_progress'],
                        'diagnosis' => ['Diagnosa', 'badge-diagnosis'],
                        'repairing' => ['Perbaikan', 'badge-repairing'],
                        'completed' => ['Selesai', 'badge-completed'],
                        'cancelled' => ['Dibatalkan', 'badge-cancelled'],
                    ];
                @endphp
                @forelse($statusDistribution as $status => $count)
                    <div class="flex items-center justify-between">
                        <span class="badge {{ $statusLabels[$status][1] ?? 'badge-pending' }}">{{ $statusLabels[$status][0] ?? $status }}</span>
                        <span class="text-sm font-semibold text-gray-700">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada data pekerjaan.</p>
                @endforelse
            </div>
        </div>

        {{-- Monthly Trend --}}
        <div class="card p-5 lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Tren Pekerjaan 6 Bulan</h3>
            <div class="space-y-3">
                @foreach($monthlyOrders as $month)
                    <div class="flex items-center gap-3">
                        <span class="w-20 text-xs text-gray-500 flex-shrink-0">{{ $month['label'] }}</span>
                        <div class="flex-1 flex items-center gap-2">
                            <div class="flex-1 h-5 bg-gray-100 rounded-full overflow-hidden">
                                @php $maxVal = max(array_column($monthlyOrders, 'total')) ?: 1; @endphp
                                <div class="h-full bg-primary-500 rounded-full" style="width: {{ ($month['total'] / $maxVal) * 100 }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 w-8 text-right">{{ $month['total'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Jadwal Hari Ini --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Jadwal Hari Ini <span class="text-sm font-normal text-gray-400">({{ $ordersTodayCount }})</span></h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Lihat Semua</a>
            </div>
            @forelse($ordersToday as $order)
                <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg transition">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-500">{{ $order->customer?->name }} · {{ $order->scheduled_at?->format('H:i') }}</p>
                    </div>
                    <span class="badge badge-{{ $order->status }} flex-shrink-0 ml-3">{{ $statusLabels[$order->status][0] ?? $order->status }}</span>
                </a>
            @empty
                <div class="py-8 text-center">
                    <p class="text-sm text-gray-400">Tidak ada jadwal pekerjaan hari ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="card p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
            @forelse($recentActivities as $activity)
                <div class="flex gap-3 py-2.5 border-b border-gray-100 last:border-0">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-100">
                        <span class="badge badge-{{ $activity->status }} !px-0 !py-0 !text-[10px] !ring-0 w-full h-full flex items-center justify-center !rounded-full !bg-transparent">
                            @if($activity->status === 'completed') ✓
                            @elseif($activity->status === 'cancelled') ✗
                            @else →
                            @endif
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-700"><span class="font-medium">{{ $activity->order?->order_number }}</span> — {{ $statusLabels[$activity->status][0] ?? $activity->status }}</p>
                        <p class="text-xs text-gray-400">{{ $activity->changer?->name }} · {{ $activity->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>

</x-layouts.app>
