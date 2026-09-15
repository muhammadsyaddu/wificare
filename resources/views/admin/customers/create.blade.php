<x-layouts.app :title="'Tambah Pelanggan'" :header="'Tambah Pelanggan Baru'">
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-6">
            @csrf
            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Informasi Akun</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-danger-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="input-field @error('name') input-error @enderror" required>
                        @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-danger-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="input-field @error('email') input-error @enderror" required>
                        @error('email')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input-field @error('phone') input-error @enderror">
                        @error('phone')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-danger-500">*</span></label>
                        <input type="password" id="password" name="password" class="input-field @error('password') input-error @enderror" required>
                        @error('password')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-danger-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" required>
                    </div>
                </div>
            </div>

            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Data Pribadi</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label><input type="text" id="nik" name="nik" value="{{ old('nik') }}" class="input-field" maxlength="16"></div>
                    <div><label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="input-field">
                            <option value="">— Pilih —</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div><label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label><input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" class="input-field"></div>
                </div>
            </div>

            <div class="card p-6 space-y-5">
                <h3 class="text-base font-semibold text-gray-900 pb-3 border-b border-gray-200">Alamat Utama</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label for="address_line" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label><textarea id="address_line" name="address_line" rows="2" class="input-field">{{ old('address_line') }}</textarea></div>
                    <div><label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label><input type="text" id="city" name="city" value="{{ old('city') }}" class="input-field"></div>
                    <div><label for="district" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label><input type="text" id="district" name="district" value="{{ old('district') }}" class="input-field"></div>
                    <div><label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label><input type="text" id="province" name="province" value="{{ old('province') }}" class="input-field"></div>
                    <div><label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label><input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="input-field" maxlength="10"></div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">Simpan Pelanggan</button>
                <a href="{{ route('admin.customers.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
