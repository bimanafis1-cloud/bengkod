<form method="POST" action="{{ $action }}" class="space-y-3">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div>
        <label for="nama_poli" class="label">
            <span class="label-text font-semibold">Nama Poli <span class="text-error">*</span></span>
        </label>
        <input id="nama_poli" name="nama_poli" type="text" maxlength="25"
            value="{{ old('nama_poli', $poli->nama_poli ?? '') }}"
            class="input input-bordered w-full @error('nama_poli') input-error @enderror"
            placeholder="Masukkan nama poli..."
            required>
        @error('nama_poli')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="keterangan" class="label">
            <span class="label-text font-semibold">Keterangan</span>
        </label>
        <textarea id="keterangan" name="keterangan" rows="4"
            class="textarea textarea-bordered w-full @error('keterangan') textarea-error @enderror"
            placeholder="Masukkan keterangan poli...">{{ old('keterangan', $poli->keterangan ?? '') }}</textarea>
        @error('keterangan')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap gap-2 pt-1">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save"></i>
            {{ $buttonText }}
        </button>
        <a href="{{ route('admin.polis.index') }}" class="btn btn-ghost btn-sm">Batal</a>
    </div>
</form>
