<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="nama" class="label">
                <span class="label-text font-semibold">Nama Pasien <span class="text-error">*</span></span>
            </label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $pasien->nama ?? '') }}"
                class="input input-bordered w-full @error('nama') input-error @enderror"
                placeholder="Masukkan nama pasien..." required>
            @error('nama')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="label">
                <span class="label-text font-semibold">Email <span class="text-error">*</span></span>
            </label>
            <input id="email" name="email" type="email" value="{{ old('email', $pasien->email ?? '') }}"
                class="input input-bordered w-full @error('email') input-error @enderror"
                placeholder="nama@example.com" required>
            @error('email')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="no_ktp" class="label">
                <span class="label-text font-semibold">No. KTP <span class="text-error">*</span></span>
            </label>
            <input id="no_ktp" name="no_ktp" type="text" maxlength="16"
                value="{{ old('no_ktp', $pasien->no_ktp ?? '') }}"
                class="input input-bordered w-full @error('no_ktp') input-error @enderror"
                placeholder="Maksimal 16 karakter" required>
            @error('no_ktp')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="no_hp" class="label">
                <span class="label-text font-semibold">No. HP <span class="text-error">*</span></span>
            </label>
            <input id="no_hp" name="no_hp" type="text" maxlength="15"
                value="{{ old('no_hp', $pasien->no_hp ?? '') }}"
                class="input input-bordered w-full @error('no_hp') input-error @enderror"
                placeholder="Maksimal 15 karakter" required>
            @error('no_hp')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="alamat" class="label">
            <span class="label-text font-semibold">Alamat <span class="text-error">*</span></span>
        </label>
        <textarea id="alamat" name="alamat" rows="3"
            class="textarea textarea-bordered w-full @error('alamat') textarea-error @enderror"
            placeholder="Masukkan alamat pasien..." required>{{ old('alamat', $pasien->alamat ?? '') }}</textarea>
        @error('alamat')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="label">
            <span class="label-text font-semibold">
                Password
                @unless (isset($pasien))
                    <span class="text-error">*</span>
                @else
                    <span class="text-base-content/60">(kosongkan jika tidak ingin mengubah)</span>
                @endunless
            </span>
            </label>
        <input id="password" name="password" type="password"
            class="input input-bordered w-full @error('password') input-error @enderror"
            autocomplete="new-password"
            @unless (isset($pasien)) required @endunless>
        @error('password')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap gap-2 pt-1">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save"></i>
            {{ $buttonText }}
        </button>
        <a href="{{ route('admin.pasien.index') }}" class="btn btn-ghost btn-sm">Batal</a>
    </div>
</form>
