<x-layouts.app title="Detail Riwayat Pasien">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('riwayat-pasien.index') }}" class="btn btn-ghost btn-sm"
            aria-label="Kembali ke riwayat pasien">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Detail Riwayat</h1>
    </div>

    <div class="space-y-4">
        <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold">Informasi Pasien</h2>
            <dl class="grid gap-3 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-base-content/60">Nama Pasien</dt>
                    <dd class="font-semibold">{{ $periksa->daftarPoli->pasien->nama }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Nomor Antrian</dt>
                    <dd class="font-semibold">{{ $periksa->daftarPoli->no_antrian }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Keluhan</dt>
                    <dd class="font-semibold">{{ $periksa->daftarPoli->keluhan ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Poli</dt>
                    <dd class="font-semibold">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Dokter</dt>
                    <dd class="font-semibold">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Tanggal Periksa</dt>
                    <dd class="font-semibold">
                        {{ \Illuminate\Support\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') }}
                    </dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
            <h2 class="mb-3 text-lg font-bold">Catatan Dokter</h2>
            <p class="whitespace-pre-line text-base-content/80">{{ $periksa->catatan ?: 'Tidak ada catatan.' }}</p>
        </section>

        <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold">Obat yang Diresepkan</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Obat</th>
                            <th>Kemasan</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periksa->detailPeriksas as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->obat->nama_obat }}</td>
                                <td>{{ $detail->obat->kemasan ?: '-' }}</td>
                                <td>Rp {{ number_format($detail->obat->harga, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-base-content/60">
                                    Tidak ada obat yang diresepkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl bg-base-100 p-6 text-center shadow-sm">
            <p class="text-sm font-semibold text-base-content/60">Total Biaya Periksa</p>
            <p class="mt-1 text-2xl font-bold text-primary">
                Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
            </p>
        </section>
    </div>
</x-layouts.app>
