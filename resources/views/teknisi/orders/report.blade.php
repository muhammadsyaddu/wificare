<x-layouts.app :title="'Laporan Pekerjaan'" :header="'Laporan & Upload Bukti'">
    <div class="mb-4"><a href="{{ route('teknisi.orders.show', $order->id) }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali ke Detail</a></div>

    <div class="max-w-2xl">
        <div class="card p-4 mb-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm font-semibold text-gray-900">{{ $order->order_number }}</p><p class="text-xs text-gray-500">{{ $order->customer?->name }} · {{ $order->items->pluck('service.name')->filter()->join(', ') }}</p></div>
            </div>
        </div>

        <form method="POST" action="{{ route('teknisi.orders.store-report', $order->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Laporan Pekerjaan</h3>

                <div>
                    <label for="diagnosis_summary" class="block text-sm font-medium text-gray-700 mb-1">Ringkasan Diagnosa <span class="text-danger-500">*</span></label>
                    <textarea id="diagnosis_summary" name="diagnosis_summary" rows="3" class="input-field @error('diagnosis_summary') input-error @enderror" required placeholder="Jelaskan temuan diagnosa dari pekerjaan ini...">{{ old('diagnosis_summary') }}</textarea>
                    @error('diagnosis_summary')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="action_taken" class="block text-sm font-medium text-gray-700 mb-1">Tindakan yang Dilakukan <span class="text-danger-500">*</span></label>
                    <textarea id="action_taken" name="action_taken" rows="3" class="input-field @error('action_taken') input-error @enderror" required placeholder="Jelaskan langkah-langkah yang dilakukan...">{{ old('action_taken') }}</textarea>
                    @error('action_taken')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="result" class="block text-sm font-medium text-gray-700 mb-1">Hasil Pekerjaan <span class="text-danger-500">*</span></label>
                    <textarea id="result" name="result" rows="3" class="input-field @error('result') input-error @enderror" required placeholder="Jelaskan hasil akhir dari pekerjaan...">{{ old('result') }}</textarea>
                    @error('result')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="technician_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea id="technician_notes" name="technician_notes" rows="2" class="input-field" placeholder="Catatan opsional...">{{ old('technician_notes') }}</textarea>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200 mb-4">Foto Bukti Pekerjaan <span class="text-danger-500">*</span></h3>
                <p class="text-sm text-gray-500 mb-4">Unggah minimal 1 foto bukti. Format: JPEG, PNG, WebP. Maks: 5MB per foto.</p>

                <div id="photo-inputs">
                    <div class="flex gap-3 items-end mb-3 photo-row">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto 1</label>
                            <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" class="input-field" required>
                        </div>
                        <div class="w-36">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select name="attachment_types[]" class="input-field">
                                <option value="before">Sebelum</option>
                                <option value="in_progress">Proses</option>
                                <option value="after" selected>Sesudah</option>
                                <option value="speedtest">Speedtest</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>
                @error('photos')<p class="text-sm text-danger-600">{{ $message }}</p>@enderror
                @error('photos.*')<p class="text-sm text-danger-600">{{ $message }}</p>@enderror

                <button type="button" onclick="addPhotoInput()" class="btn-secondary btn-sm mt-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Foto
                </button>
            </div>

            <button type="submit" class="btn-success w-full justify-center text-base py-3">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Selesaikan Pekerjaan & Kirim Laporan
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        let photoCount = 1;
        function addPhotoInput() {
            if (photoCount >= 10) { alert('Maksimal 10 foto.'); return; }
            photoCount++;
            const container = document.getElementById('photo-inputs');
            const div = document.createElement('div');
            div.className = 'flex gap-3 items-end mb-3 photo-row';
            div.innerHTML = `<div class="flex-1"><label class="block text-sm font-medium text-gray-700 mb-1">Foto ${photoCount}</label><input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" class="input-field" required></div><div class="w-36"><label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label><select name="attachment_types[]" class="input-field"><option value="before">Sebelum</option><option value="in_progress">Proses</option><option value="after" selected>Sesudah</option><option value="speedtest">Speedtest</option><option value="other">Lainnya</option></select></div><button type="button" onclick="this.parentElement.remove()" class="btn-icon text-red-500 mb-1">&times;</button>`;
            container.appendChild(div);
        }
    </script>
    @endpush
</x-layouts.app>
