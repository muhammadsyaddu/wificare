<x-layouts.app :title="'Edit Pelanggan'" :header="'Edit Pelanggan'">
    <div class="mb-4"><a href="{{ route('admin.customers.show', $customer->id) }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali</a></div>
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="space-y-6">
            @csrf @method('PUT')
            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Informasi Akun</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-danger-500">*</span></label><input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" class="input-field @error('name') input-error @enderror" required>@error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror</div>
                    <div><label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-danger-500">*</span></label><input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" class="input-field @error('email') input-error @enderror" required>@error('email')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror</div>
                    <div><label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label><input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" class="input-field"></div>
                    <div><label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="is_active" name="is_active" class="input-field">
                            <option value="1" {{ old('is_active', $customer->is_active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('is_active', $customer->is_active) ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Data Pribadi</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label><input type="text" id="nik" name="nik" value="{{ old('nik', $customer->customerProfile?->nik) }}" class="input-field" maxlength="16"></div>
                    <div><label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="input-field">
                            <option value="">— Pilih —</option>
                            @foreach(['male' => 'Laki-laki', 'female' => 'Perempuan', 'other' => 'Lainnya'] as $v => $l)
                                <option value="{{ $v }}" {{ old('gender', $customer->customerProfile?->gender) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label><input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $customer->customerProfile?->birth_date?->format('Y-m-d')) }}" class="input-field"></div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
