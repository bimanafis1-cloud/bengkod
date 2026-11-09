<x-layouts.app title="Riwayat Pasien">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wide text-primary">Dokter</p>
            <h1 class="mt-2 text-2xl font-bold text-base-content">Riwayat Pasien</h1>
            <p class="mt-1 text-base-content/70">Riwayat pemeriksaan pasien yang telah Anda tangani.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pasien</th>
                        <th>Keluhan</th>
                        <th>Tanggal Pemeriksaan</th>
                        <th>Biaya Pemeriksaan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPasien as $periksa)
                        <tr>
                            <td>{{ $periksa->id }}</td>
                            <td class="font-semibold">{{ $periksa->daftarPoli->pasien->nama }}</td>
                            <td>{{ $periksa->daftarPoli->keluhan ?: '-' }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') }}</td>
                            <td>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('riwayat-pasien.show', $periksa) }}"
                                        class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-base-content/60">
                                Belum ada riwayat pemeriksaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
