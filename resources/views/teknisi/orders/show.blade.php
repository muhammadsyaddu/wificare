<x-layouts.app :title="$order->order_number" :header="'Detail Tugas'">
    @php
        $sl = ['assigned'=>['Menunggu Konfirmasi','badge-assigned'],'accepted'=>['Diterima','badge-accepted'],'on_the_way'=>['Dalam Perjalanan','badge-on_the_way'],'in_progress'=>['Sedang Dikerjakan','badge-in_progress'],'diagnosis'=>['Proses Diagnosa','badge-diagnosis'],'repairing'=>['Proses Perbaikan','badge-repairing'],'completed'=>['Selesai','badge-completed'],'cancelled'=>['Dibatalkan','badge-cancelled'],'pending'=>['Pending','badge-pending'],'confirmed'=>['Dikonfirmasi','badge-confirmed']];
        $transitions = [
            'accepted' => ['on_the_way' => 'Mulai Perjalanan'],
            'on_the_way' => ['in_progress' => 'Mulai Pengerjaan'],
            'in_progress' => ['diagnosis' => 'Lakukan Diagnosa', 'repairing' => 'Mulai Perbaikan'],
            'diagnosis' => ['repairing' => 'Mulai Perbaikan'],
        ];
    @endphp
    <div class="mb-4"><a href="{{ route('teknisi.orders.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali</a></div>

    {{-- Header --}}
    <div class="card p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-500">{{ $order->scheduled_at?->translatedFormat('l, d F Y H:i') }}</p>
            </div>
            <span class="badge {{ $sl[$order->status][1] ?? '' }} text-sm px-3 py-1">{{ $sl[$order->status][0] ?? $order->status }}</span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 text-sm">
            <div><span class="text-gray-500">Pelanggan</span><p class="font-medium text-gray-900">{{ $order->customer?->name }}</p><p class="text-xs text-gray-500">{{ $order->customer?->phone }} · {{ $order->customer?->email }}</p></div>
            <div><span class="text-gray-500">Layanan</span>
                @foreach($order->items as $item)<p class="font-medium text-gray-900">{{ $item->service?->name }} ({{ $item->quantity }}x)</p>@endforeach
            </div>
        </div>

        @if($order->address_snapshot)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-500">Lokasi Pekerjaan</span>
                <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $order->address_snapshot['address_line'] ?? '' }}</p>
                <p class="text-xs text-gray-500">{{ $order->address_snapshot['district'] ?? '' }}, {{ $order->address_snapshot['city'] ?? '' }}, {{ $order->address_snapshot['province'] ?? '' }}</p>
                @if(!empty($order->address_snapshot['benchmark_notes']))<p class="text-xs text-amber-700 mt-1 bg-amber-50 rounded px-2 py-1 inline-block">Patokan: {{ $order->address_snapshot['benchmark_notes'] }}</p>@endif
            </div>
        @endif

        @if($order->notes)
            <div class="mt-4 pt-4 border-t border-gray-100"><span class="text-sm text-gray-500">Catatan</span><p class="text-sm text-gray-700 mt-0.5">{{ $order->notes }}</p></div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="space-y-3 mb-6">
        {{-- Accept --}}
        @if($order->status === 'assigned')
            <form method="POST" action="{{ route('teknisi.orders.accept', $order->id) }}">
                @csrf @method('PUT')
                <button type="submit" class="btn-success w-full justify-center text-base py-3">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Terima Penugasan
                </button>
            </form>
        @endif

        {{-- Status Update --}}
        @if(isset($transitions[$order->status]))
            @foreach($transitions[$order->status] as $nextStatus => $label)
                <form method="POST" action="{{ route('teknisi.orders.update-status', $order->id) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    <button type="submit" class="btn-primary w-full justify-center text-base py-3">{{ $label }}</button>
                </form>
            @endforeach
        @endif

        {{-- Complete / Report --}}
        @if(in_array($order->status, ['in_progress', 'diagnosis', 'repairing']) && !$order->workReport)
            <a href="{{ route('teknisi.orders.report', $order->id) }}" class="btn-success w-full justify-center text-base py-3 inline-flex">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                Selesaikan & Upload Bukti
            </a>
        @endif
    </div>

    {{-- Work Report (if exists) --}}
    @if($order->workReport)
        <div class="card p-5 mb-6">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Laporan Pekerjaan</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">Diagnosa</dt><dd class="text-gray-900 mt-0.5">{{ $order->workReport->diagnosis_summary }}</dd></div>
                <div><dt class="text-gray-500">Tindakan</dt><dd class="text-gray-900 mt-0.5">{{ $order->workReport->action_taken }}</dd></div>
                <div><dt class="text-gray-500">Hasil</dt><dd class="text-gray-900 mt-0.5">{{ $order->workReport->result }}</dd></div>
            </dl>
            @if($order->workReport->attachments->count())
                <div class="mt-4 grid grid-cols-3 gap-2">
                    @foreach($order->workReport->attachments as $att)
                        <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->attachment_type }}" class="h-24 w-full rounded-lg object-cover border border-gray-200">
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Status Timeline --}}
    <div class="card p-5">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Riwayat Status</h3>
        @foreach($order->statusHistories->sortByDesc('created_at') as $history)
            <div class="flex gap-3 pb-3 last:pb-0">
                <div class="flex flex-col items-center"><div class="h-2.5 w-2.5 rounded-full {{ $history->status === 'completed' ? 'bg-emerald-500' : 'bg-primary-500' }} flex-shrink-0 mt-1.5"></div>@if(!$loop->last)<div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>@endif</div>
                <div class="pb-2"><p class="text-sm font-medium text-gray-900">{{ $sl[$history->status][0] ?? $history->status }}</p>@if($history->notes)<p class="text-xs text-gray-500">{{ $history->notes }}</p>@endif<p class="text-xs text-gray-400">{{ $history->created_at->translatedFormat('d M Y H:i') }}</p></div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
