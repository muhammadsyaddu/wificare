<x-layouts.app :title="'Buat Pesanan'" :header="'Buat Pesanan Baru'">
    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6" id="orderForm">
            @csrf
            @if($issue)<input type="hidden" name="customer_issue_id" value="{{ $issue->id }}">@endif

            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Informasi Pelanggan</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Pelanggan <span class="text-danger-500">*</span></label>
                        <select id="customer_id" name="customer_id" class="input-field @error('customer_id') input-error @enderror" required onchange="loadAddresses(this.value)">
                            <option value="">— Pilih Pelanggan —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $issue?->user_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                        @error('customer_id')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="address_id" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-danger-500">*</span></label>
                        <select id="address_id" name="address_id" class="input-field @error('address_id') input-error @enderror" required>
                            <option value="">— Pilih pelanggan dulu —</option>
                        </select>
                        @error('address_id')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                @if($issue)
                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-3">
                        <p class="text-sm font-medium text-amber-800">Berdasarkan laporan gangguan:</p>
                        <p class="text-sm text-amber-700 mt-1">{{ $issue->title }}</p>
                    </div>
                @endif
            </div>

            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Layanan</h3>
                <div id="services-container">
                    <div class="flex gap-3 items-end service-row mb-3">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan <span class="text-danger-500">*</span></label>
                            <select name="services[0][id]" class="input-field" required>
                                <option value="">— Pilih Layanan —</option>
                                @foreach($services as $svc)<option value="{{ $svc->id }}">{{ $svc->name }} — Rp {{ number_format($svc->current_price, 0, ',', '.') }}</option>@endforeach
                            </select>
                        </div>
                        <div class="w-24">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                            <input type="number" name="services[0][qty]" value="1" min="1" class="input-field" required>
                        </div>
                    </div>
                </div>
                @error('services')<p class="text-sm text-danger-600">{{ $message }}</p>@enderror
            </div>

            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Jadwal & Penugasan</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Jadwal Pekerjaan <span class="text-danger-500">*</span></label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}" class="input-field @error('scheduled_at') input-error @enderror" required>
                        @error('scheduled_at')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-1">Teknisi</label>
                        <select id="technician_id" name="technician_id" class="input-field">
                            <option value="">— Belum ditugaskan —</option>
                            @foreach($technicians as $t)<option value="{{ $t->id }}" {{ old('technician_id') == $t->id ? 'selected' : '' }}>{{ $t->user?->name }} ({{ $t->technician_code }})</option>@endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea id="notes" name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
                    </div>
                    <div>
                        <label for="service_fee" class="block text-sm font-medium text-gray-700 mb-1">Biaya Layanan (Rp)</label>
                        <input type="number" id="service_fee" name="service_fee" value="{{ old('service_fee', 15000) }}" min="0" class="input-field">
                    </div>
                    <div>
                        <label for="discount" class="block text-sm font-medium text-gray-700 mb-1">Diskon (Rp)</label>
                        <input type="number" id="discount" name="discount" value="{{ old('discount', 0) }}" min="0" class="input-field">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">Buat Pesanan</button>
                <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function loadAddresses(customerId) {
            const select = document.getElementById('address_id');
            select.innerHTML = '<option value="">Memuat alamat...</option>';
            if (!customerId) { select.innerHTML = '<option value="">— Pilih pelanggan dulu —</option>'; return; }
            fetch(`/admin/api/customers/${customerId}/addresses`)
                .then(r => r.json())
                .then(addresses => {
                    if (addresses.length === 0) { select.innerHTML = '<option value="">Belum ada alamat</option>'; return; }
                    select.innerHTML = '<option value="">— Pilih Alamat —</option>';
                    addresses.forEach(a => {
                        const opt = document.createElement('option');
                        opt.value = a.id;
                        opt.textContent = `${a.label} — ${a.address_line}, ${a.city}`;
                        select.appendChild(opt);
                    });
                });
        }
        // Auto-load if customer pre-selected
        const initCustomer = document.getElementById('customer_id').value;
        if (initCustomer) loadAddresses(initCustomer);
    </script>
    @endpush
</x-layouts.app>
