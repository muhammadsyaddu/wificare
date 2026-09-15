<x-layouts.app :title="$technician->name" :header="'Detail Teknisi'">
    <div class="mb-4"><a href="{{ route('admin.technicians.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali ke Daftar Teknisi</a></div>
    @php $p = $technician->technicianProfile; $vl = ['pending'=>'Pending','verified'=>'Terverifikasi','rejected'=>'Ditolak','suspended'=>'Ditangguhkan']; $vs = ['pending'=>'badge-pending','verified'=>'badge-completed','rejected'=>'badge-cancelled','suspended'=>'badge-on_the_way']; @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-1 space-y-5">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-lg font-bold text-indigo-700">{{ strtoupper(substr($technician->name, 0, 2)) }}</div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $technician->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $p?->technician_code }}</p>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $technician->email }}</dd></div>
                <div><dt class="text-gray-500">Telepon</dt><dd class="font-medium">{{ $p?->phone ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Spesialisasi</dt><dd class="font-medium">{{ $p?->specialization ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Pengalaman</dt><dd class="font-medium">{{ $p?->experience_years ?? 0 }} tahun</dd></div>
                <div class="flex items-center justify-between"><dt class="text-gray-500">Status Verifikasi</dt><dd><span class="badge {{ $vs[$p?->verification_status] ?? '' }}">{{ $vl[$p?->verification_status] ?? '—' }}</span></dd></div>
                <div class="flex items-center justify-between"><dt class="text-gray-500">Ketersediaan</dt><dd class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full {{ $p?->is_available ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>{{ $p?->is_available ? 'Tersedia' : 'Tidak Tersedia' }}</dd></div>
            </dl>

            {{-- Actions --}}
            <div class="pt-4 border-t border-gray-200 space-y-2">
                @if($p && $p->verification_status === 'pending')
                    <form method="POST" action="{{ route('admin.technicians.verify', $technician->id) }}">
                        @csrf <input type="hidden" name="action" value="verify">
                        <button type="submit" class="btn-success btn-sm w-full justify-center">Verifikasi Teknisi</button>
                    </form>
                @endif
                @if($p)
                    <form method="POST" action="{{ route('admin.technicians.toggle-availability', $technician->id) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="btn-secondary btn-sm w-full justify-center">{{ $p->is_available ? 'Set Tidak Tersedia' : 'Set Tersedia' }}</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            {{-- Statistik --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="card px-4 py-3 text-center"><p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p><p class="text-xs text-gray-500">Total Order</p></div>
                <div class="card px-4 py-3 text-center"><p class="text-2xl font-bold text-emerald-600">{{ $stats['completed_orders'] }}</p><p class="text-xs text-gray-500">Selesai</p></div>
                <div class="card px-4 py-3 text-center"><p class="text-2xl font-bold text-amber-600">{{ $stats['active_orders'] }}</p><p class="text-xs text-gray-500">Aktif</p></div>
                <div class="card px-4 py-3 text-center"><p class="text-2xl font-bold text-primary-600">{{ number_format($stats['avg_rating'], 1) }}</p><p class="text-xs text-gray-500">Rating</p></div>
            </div>

            {{-- Dokumen --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Dokumen Sertifikasi</h3>
                @forelse($p?->documents ?? [] as $doc)
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100 last:border-0">
                        <div><p class="text-sm font-medium text-gray-900">{{ $doc->document_name }}</p><p class="text-xs text-gray-500">{{ strtoupper($doc->document_type) }} · {{ number_format($doc->file_size / 1024) }} KB</p></div>
                        <span class="badge {{ $doc->is_verified ? 'badge-completed' : 'badge-pending' }}">{{ $doc->is_verified ? 'Terverifikasi' : 'Pending' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada dokumen.</p>
                @endforelse
            </div>

            {{-- Pekerjaan Terbaru --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Pekerjaan Terbaru</h3>
                @php $sl = ['pending'=>['Pending','badge-pending'],'confirmed'=>['Dikonfirmasi','badge-confirmed'],'assigned'=>['Ditugaskan','badge-assigned'],'accepted'=>['Diterima','badge-accepted'],'on_the_way'=>['Perjalanan','badge-on_the_way'],'in_progress'=>['Dikerjakan','badge-in_progress'],'diagnosis'=>['Diagnosa','badge-diagnosis'],'repairing'=>['Perbaikan','badge-repairing'],'completed'=>['Selesai','badge-completed'],'cancelled'=>['Dibatalkan','badge-cancelled']]; @endphp
                @forelse($p?->orders ?? [] as $order)
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center justify-between py-2.5 border-b border-gray-100 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg">
                        <div><p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p><p class="text-xs text-gray-500">{{ $order->customer?->name }} · {{ $order->scheduled_at?->translatedFormat('d M Y') }}</p></div>
                        <span class="badge {{ $sl[$order->status][1] ?? '' }}">{{ $sl[$order->status][0] ?? $order->status }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400">Belum ada pekerjaan.</p>
                @endforelse
            </div>

            {{-- Rating Terbaru --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Ulasan Terbaru</h3>
                @forelse($p?->ratings ?? [] as $rating)
                    <div class="py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="flex gap-0.5">@for($i = 1; $i <= 5; $i++)<svg class="h-4 w-4 {{ $i <= $rating->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor</div>
                            <span class="text-sm font-medium text-gray-700">{{ $rating->customer?->name }}</span>
                        </div>
                        @if($rating->review)<p class="text-sm text-gray-600">{{ $rating->review }}</p>@endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada ulasan.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
