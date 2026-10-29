<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div>
        <label for="hari" class="label">
            <span class="label-text font-semibold">Hari <span class="text-error">*</span></span>
        </label>
        <select id="hari" name="hari"
            class="select select-bordered w-full @error('hari') select-error @enderror" required>
            <option value="">Pilih hari</option>
            @foreach ($hariList as $hari)
                <option value="{{ $hari }}" @selected(old('hari', $jadwal->hari ?? '') === $hari)>
                    {{ $hari }}
                </option>
            @endforeach
        </select>
        @error('hari')
            <p class="mt-1 text-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="jam_mulai" class="label">
                <span class="label-text font-semibold">Jam Mulai <span class="text-error">*</span></span>
            </label>
            <input id="jam_mulai" name="jam_mulai" type="time"
                value="{{ old('jam_mulai', isset($jadwal) ? substr($jadwal->jam_mulai, 0, 5) : '') }}"
                class="input input-bordered w-full @error('jam_mulai') input-error @enderror" required>
            @error('jam_mulai')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="jam_selesai" class="label">
                <span class="label-text font-semibold">Jam Selesai <span class="text-error">*</span></span>
            </label>
            <input id="jam_selesai" name="jam_selesai" type="time"
                value="{{ old('jam_selesai', isset($jadwal) ? substr($jadwal->jam_selesai, 0, 5) : '') }}"
                class="input input-bordered w-full @error('jam_selesai') input-error @enderror" required>
            @error('jam_selesai')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-wrap gap-2 pt-1">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save"></i>
            {{ $buttonText }}
        </button>
        <a href="{{ route('jadwal-periksa.index') }}" class="btn btn-ghost btn-sm">Batal</a>
    </div>
</form>
