<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div>
        <label for="nama_obat" class="label">
            <span class="label-text font-semibold">Nama Obat <span class="text-error">*</span></span>
        </label>
        <input id="nama_obat" name="nama_obat" type="text"
            value="{{ old('nama_obat', $obat->nama_obat ?? '') }}"
            class="input input-bordered w-full @error('nama_obat') input-error @enderror"
            placeholder="Masukkan nama obat..." required>
        @error('nama_obat')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kemasan" class="label">
            <span class="label-text font-semibold">Kemasan</span>
        </label>
        <input id="kemasan" name="kemasan" type="text" maxlength="35"
            value="{{ old('kemasan', $obat->kemasan ?? '') }}"
            class="input input-bordered w-full @error('kemasan') input-error @enderror"
            placeholder="Contoh: Tablet, Botol (opsional)">
        @error('kemasan')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="harga" class="label">
            <span class="label-text font-semibold">Harga (Rp) <span class="text-error">*</span></span>
        </label>
        <input id="harga" name="harga" type="number" min="0" step="1"
            value="{{ old('harga', $obat->harga ?? '') }}"
            class="input input-bordered w-full @error('harga') input-error @enderror"
            placeholder="Masukkan harga obat..." required>
        @error('harga')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap gap-2 pt-1">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save"></i>
            {{ $buttonText }}
        </button>
        <a href="{{ route('obat.index') }}" class="btn btn-ghost btn-sm">Batal</a>
    </div>
</form>
