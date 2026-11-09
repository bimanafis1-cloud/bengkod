<x-layouts.app title="Pendaftaran Poli">
    <section class="mx-auto max-w-3xl rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wide text-primary">Pasien</p>
            <h1 class="mt-2 text-2xl font-bold text-base-content">Pendaftaran Poli</h1>
            <p class="mt-1 text-base-content/70">Pilih poli dan jadwal pemeriksaan yang tersedia.</p>
        </div>

        <form method="POST" action="{{ route('pasien.daftar.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="no_rm" class="label">
                    <span class="label-text font-semibold">Nomor Rekam Medis</span>
                </label>
                <input id="no_rm" type="text" value="{{ $user->no_rm ?: '-' }}"
                    class="input input-bordered w-full" readonly>
            </div>

            <div>
                <label for="id_poli" class="label">
                    <span class="label-text font-semibold">Pilih Poli <span class="text-error">*</span></span>
                </label>
                <select id="id_poli" name="id_poli"
                    class="select select-bordered w-full @error('id_poli') select-error @enderror" required>
                    <option value="">Pilih poli</option>
                    @foreach ($polis as $poli)
                        <option value="{{ $poli->id }}" @selected((string) old('id_poli') === (string) $poli->id)>
                            {{ $poli->nama_poli }}
                        </option>
                    @endforeach
                </select>
                @error('id_poli')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="id_jadwal" class="label">
                    <span class="label-text font-semibold">Pilih Jadwal Periksa <span class="text-error">*</span></span>
                </label>
                <select id="id_jadwal" name="id_jadwal"
                    class="select select-bordered w-full @error('id_jadwal') select-error @enderror" required>
                    <option value="">Pilih jadwal</option>
                    @foreach ($jadwals as $jadwal)
                        @if ($jadwal->dokter)
                            <option value="{{ $jadwal->id }}"
                                data-poli="{{ $jadwal->dokter->id_poli }}"
                                @selected((string) old('id_jadwal') === (string) $jadwal->id)>
                                {{ $jadwal->hari }} | {{ substr($jadwal->jam_mulai, 0, 5) }}–{{ substr($jadwal->jam_selesai, 0, 5) }}
                                | {{ $jadwal->dokter->nama }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('id_jadwal')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="keluhan" class="label">
                    <span class="label-text font-semibold">Keluhan (opsional)</span>
                </label>
                <textarea id="keluhan" name="keluhan" rows="4"
                    class="textarea textarea-bordered w-full @error('keluhan') textarea-error @enderror"
                    placeholder="Tuliskan keluhan Anda...">{{ old('keluhan') }}</textarea>
                @error('keluhan')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-calendar-check"></i>
                Daftar Poli
            </button>
        </form>
    </section>

    @push('scripts')
        <script>
            const poliSelect = document.getElementById('id_poli');
            const jadwalSelect = document.getElementById('id_jadwal');

            function filterJadwal() {
                const poliId = poliSelect.value;
                Array.from(jadwalSelect.options).forEach((option, index) => {
                    if (index === 0) return;
                    option.hidden = !poliId || option.dataset.poli !== poliId;
                    if (option.hidden && option.selected) jadwalSelect.value = '';
                });
            }

            poliSelect.addEventListener('change', filterJadwal);
            filterJadwal();
        </script>
    @endpush
</x-layouts.app>
