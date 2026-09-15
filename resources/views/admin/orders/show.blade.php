<x-layouts.app :title="$order->order_number" :header="'Detail Pekerjaan'">
    @php $sl = ['pending'=>['Pending','badge-pending'],'confirmed'=>['Dikonfirmasi','badge-confirmed'],'assigned'=>['Ditugaskan','badge-assigned'],'accepted'=>['Diterima','badge-accepted'],'on_the_way'=>['Perjalanan','badge-on_the_way'],'in_progress'=>['Dikerjakan','badge-in_progress'],'diagnosis'=>['Diagnosa','badge-diagnosis'],'repairing'=>['Perbaikan','badge-repairing'],'completed'=>['Selesai','badge-completed'],'cancelled'=>['Dibatalkan','badge-cancelled']]; @endphp
    <div class="mb-4"><a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali</a></div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500">Dibuat {{ $order->created_at->translatedFormat('d F Y H:i') }}</p>
                    </div>
                    <span class="badge {{ $sl[$order->status][1] ?? '' }} text-sm px-3 py-1">{{ $sl[$order->status][0] ?? $order->status }}</span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div><span class="text-gray-500">Pelanggan</span><p class="font-medium text-gray-900">{{ $order->customer?->name }}</p><p class="text-xs text-gray-500">{{ $order->customer?->email }} · {{ $order->customer?->phone }}</p></div>
                    <div><span class="text-gray-500">Teknisi</span><p class="font-medium text-gray-900">{{ $order->technician?->user?->name ?? 'Belum ditugaskan' }}</p></div>
                    <div><span class="text-gray-500">Jadwal</span><p class="font-medium text-gray-900">{{ $order->scheduled_at?->translatedFormat('d F Y H:i') ?? '—' }}</p></div>
                    <div><span class="text-gray-500">Total</span><p class="font-medium text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p></div>
                </div>

                @if($order->address_snapshot)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500 mb-1">Alamat Pekerjaan</p>
                        <p class="text-sm text-gray-900">{{ $order->address_snapshot['address_line'] ?? '' }}, {{ $order->address_snapshot['district'] ?? '' }}, {{ $order->address_snapshot['city'] ?? '' }}, {{ $order->address_snapshot['province'] ?? '' }}</p>
                        @if(!empty($order->address_snapshot['benchmark_notes']))<p class="text-xs text-gray-500 mt-1">Patokan: {{ $order->address_snapshot['benchmark_notes'] }}</p>@endif
                    </div>
                @endif

                @if($order->notes)<div class="mt-4 pt-4 border-t border-gray-100"><p class="text-sm text-gray-500 mb-1">Catatan</p><p class="text-sm text-gray-700">{{ $order->notes }}</p></div>@endif
            </div>

            {{-- Items --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Layanan</h3>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between py-2.5">
                            <div><p class="text-sm font-medium text-gray-900">{{ $item->service?->name ?? '—' }}</p><p class="text-xs text-gray-500">{{ $item->quantity }}x @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p></div>
                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-200 mt-2 pt-3 space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Biaya Layanan</span><span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span></div>
                    @if($order->discount > 0)<div class="flex justify-between"><span class="text-gray-500">Diskon</span><span class="text-emerald-600">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span></div>@endif
                    <div class="flex justify-between font-semibold text-gray-900 pt-1"><span>Total</span><span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                </div>
            </div>

            {{-- Work Report --}}
            @if($order->workReport)
                <div class="card p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Laporan Pekerjaan</h3>
                    <dl class="space-y-3 text-sm">
                        <div><dt class="text-gray-500">Ringkasan Diagnosa</dt><dd class="text-gray-900 mt-0.5">{{ $order->workReport->diagnosis_summary }}</dd></div>
                        <div><dt class="text-gray-500">Tindakan</dt><dd class="text-gray-900 mt-0.5">{{ $order->workReport->action_taken }}</dd></div>
                        <div><dt class="text-gray-500">Hasil</dt><dd class="text-gray-900 mt-0.5">{{ $order->workReport->result }}</dd></div>
                        @if($order->workReport->technician_notes)<div><dt class="text-gray-500">Catatan Teknisi</dt><dd class="text-gray-900 mt-0.5">{{ $order->workReport->technician_notes }}</dd></div>@endif
                    </dl>
                    @if($order->workReport->attachments->count())
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm font-medium text-gray-700 mb-2">Bukti Foto ({{ $order->workReport->attachments->count() }})</p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                @foreach($order->workReport->attachments as $att)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->attachment_type }}" class="h-24 w-full rounded-lg object-cover border border-gray-200">
                                        <span class="absolute bottom-1 left-1 badge badge-confirmed text-[10px] px-1.5 py-0.5">{{ $att->attachment_type }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Timeline --}}
            <div class="card p-5">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Riwayat Status</h3>
                <div class="space-y-0">
                    @foreach($order->statusHistories->sortByDesc('created_at') as $history)
                        <div class="flex gap-3 pb-4 last:pb-0 relative">
                            <div class="flex flex-col items-center">
                                <div class="h-3 w-3 rounded-full {{ $history->status === 'completed' ? 'bg-emerald-500' : ($history->status === 'cancelled' ? 'bg-red-500' : 'bg-primary-500') }} flex-shrink-0 mt-1"></div>
                                @if(!$loop->last)<div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>@endif
                            </div>
                            <div class="min-w-0 flex-1 pb-2">
                                <p class="text-sm font-medium text-gray-900">{{ $sl[$history->status][0] ?? $history->status }}</p>
                                @if($history->notes)<p class="text-xs text-gray-600 mt-0.5">{{ $history->notes }}</p>@endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ $history->changer?->name }} · {{ $history->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            {{-- Assign Technician --}}
            @if(in_array($order->status, ['pending', 'confirmed']))
                <div class="card p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Tugaskan Teknisi</h3>
                    <form method="POST" action="{{ route('admin.orders.assign', $order->id) }}">
                        @csrf
                        <select name="technician_id" class="input-field mb-3" required>
                            <option value="">— Pilih Teknisi —</option>
                            @foreach($availableTechnicians as $t)<option value="{{ $t->id }}">{{ $t->user?->name }} ({{ $t->technician_code }})</option>@endforeach
                        </select>
                        @error('technician_id')<p class="mb-2 text-sm text-danger-600">{{ $message }}</p>@enderror
                        <button type="submit" class="btn-primary w-full justify-center">Tugaskan</button>
                    </form>
                </div>
            @endif

            {{-- Cancel --}}
            @if(!in_array($order->status, ['completed', 'cancelled']))
                <div class="card p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Batalkan Pesanan</h3>
                    <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}" onsubmit="return confirm('Yakin membatalkan pesanan ini?')">
                        @csrf
                        <textarea name="cancellation_reason" rows="2" class="input-field mb-3" placeholder="Alasan pembatalan..." required></textarea>
                        @error('cancellation_reason')<p class="mb-2 text-sm text-danger-600">{{ $message }}</p>@enderror
                        <button type="submit" class="btn-danger w-full justify-center btn-sm">Batalkan Pesanan</button>
                    </form>
                </div>
            @endif

            {{-- Rating --}}
            @if($order->rating)
                <div class="card p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Rating</h3>
                    <div class="flex gap-0.5 mb-2">@for($i = 1; $i <= 5; $i++)<svg class="h-5 w-5 {{ $i <= $order->rating->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor</div>
                    @if($order->rating->review)<p class="text-sm text-gray-700">{{ $order->rating->review }}</p>@endif
                    <p class="text-xs text-gray-400 mt-1">{{ $order->rating->customer?->name }}</p>
                </div>
            @endif

            {{-- Customer Issue --}}
            @if($order->customerIssue)
                <div class="card p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Laporan Gangguan</h3>
                    <p class="text-sm font-medium text-gray-900">{{ $order->customerIssue->title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $order->customerIssue->category?->name }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ Str::limit($order->customerIssue->description, 200) }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
