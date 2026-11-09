<x-layouts.app title="Periksa Pasien">
    @php($oldObatIds = json_decode(old('obat_json', '[]'), true))

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('periksa-pasien.index') }}" class="btn btn-ghost btn-sm"
            aria-label="Kembali ke daftar pasien">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Periksa Pasien</h1>
    </div>

    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6 rounded-xl bg-base-200 p-4">
            <p><span class="font-semibold">Pasien:</span> {{ $daftarPoli->pasien->nama }}</p>
            <p><span class="font-semibold">No. Antrian:</span> {{ $daftarPoli->no_antrian }}</p>
            <p><span class="font-semibold">Keluhan:</span> {{ $daftarPoli->keluhan ?: '-' }}</p>
        </div>

        <form method="POST" action="{{ route('periksa-pasien.store') }}" class="space-y-5"
            id="form-periksa">
            @csrf
            <input type="hidden" name="id_daftar_poli" value="{{ $daftarPoli->id }}">
            <input type="hidden" name="obat_json" id="obat_json"
                value="{{ json_encode(is_array($oldObatIds) ? $oldObatIds : []) }}">
            <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="0">

            <div>
                <label for="pilih_obat" class="label">
                    <span class="label-text font-semibold">Pilih Obat</span>
                </label>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <select id="pilih_obat" class="select select-bordered w-full">
                        <option value="">Pilih obat</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}" data-nama="{{ $obat->nama_obat }}"
                                data-harga="{{ $obat->harga }}">
                                {{ $obat->nama_obat }} — Rp {{ number_format($obat->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="tambah-obat" class="btn btn-secondary">Tambah Obat</button>
                </div>
            </div>

            <div>
                <h2 class="mb-2 font-semibold">Obat Terpilih</h2>
                <div class="overflow-x-auto rounded-lg border border-base-300">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Obat</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="daftar-obat">
                            <tr id="obat-kosong">
                                <td colspan="3" class="text-center text-base-content/60">Belum ada obat dipilih.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @error('obat_json')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
                @error('obat_ids')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl bg-base-200 p-4">
                <p>Total harga obat: <span id="total-obat">Rp 0</span></p>
                <p>Jasa dokter: Rp 150.000</p>
                <p class="mt-1 font-bold">Total biaya: <span id="total-biaya">Rp 150.000</span></p>
            </div>

            <div>
                <label for="catatan" class="label">
                    <span class="label-text font-semibold">Catatan (opsional)</span>
                </label>
                <textarea id="catatan" name="catatan" rows="4"
                    class="textarea textarea-bordered w-full @error('catatan') textarea-error @enderror"
                    placeholder="Masukkan catatan pemeriksaan...">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan
                </button>
                <a href="{{ route('periksa-pasien.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </section>

    @push('scripts')
        <script>
            const selectedObats = new Map();
            const medicineSelect = document.getElementById('pilih_obat');
            const medicineList = document.getElementById('daftar-obat');
            const formatRupiah = value => `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;

            function renderObats() {
                medicineList.replaceChildren();
                let total = 0;

                if (selectedObats.size === 0) {
                    const row = document.createElement('tr');
                    const cell = document.createElement('td');
                    cell.colSpan = 3;
                    cell.className = 'text-center text-base-content/60';
                    cell.textContent = 'Belum ada obat dipilih.';
                    row.append(cell);
                    medicineList.append(row);
                }

                selectedObats.forEach(obat => {
                    total += obat.harga;
                    const row = document.createElement('tr');
                    const nameCell = document.createElement('td');
                    const priceCell = document.createElement('td');
                    const actionCell = document.createElement('td');
                    const removeButton = document.createElement('button');

                    nameCell.textContent = obat.nama;
                    priceCell.textContent = formatRupiah(obat.harga);
                    removeButton.type = 'button';
                    removeButton.className = 'btn btn-sm btn-error';
                    removeButton.textContent = 'Hapus';
                    removeButton.addEventListener('click', () => {
                        selectedObats.delete(obat.id);
                        renderObats();
                    });
                    actionCell.append(removeButton);
                    row.append(nameCell, priceCell, actionCell);
                    medicineList.append(row);
                });

                document.getElementById('total-obat').textContent = formatRupiah(total);
                document.getElementById('total-biaya').textContent = formatRupiah(total + 150000);
                document.getElementById('biaya_periksa').value = total;
                document.getElementById('obat_json').value = JSON.stringify(Array.from(selectedObats.keys()));
            }

            document.getElementById('tambah-obat').addEventListener('click', () => {
                const option = medicineSelect.selectedOptions[0];
                if (!option.value || selectedObats.has(option.value)) return;

                selectedObats.set(option.value, {
                    id: Number(option.value),
                    nama: option.dataset.nama,
                    harga: Number(option.dataset.harga),
                });
                renderObats();
                medicineSelect.value = '';
            });

            const oldObatIds = JSON.parse(document.getElementById('obat_json').value);
            oldObatIds.forEach(id => {
                const option = medicineSelect.querySelector(`option[value="${Number(id)}"]`);
                if (!option) return;
                selectedObats.set(option.value, {
                    id: Number(option.value),
                    nama: option.dataset.nama,
                    harga: Number(option.dataset.harga),
                });
            });

            renderObats();
        </script>
    @endpush
</x-layouts.app>
