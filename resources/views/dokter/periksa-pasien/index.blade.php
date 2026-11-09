<x-layouts.app title="Periksa Pasien">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wide text-primary">Dokter</p>
            <h1 class="mt-2 text-2xl font-bold text-base-content">Periksa Pasien</h1>
            <p class="mt-1 text-base-content/70">Daftar pasien yang terdaftar pada jadwal praktik Anda.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pasien</th>
                        <th>Keluhan</th>
                        <th>No. Antrian</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPasien as $daftar)
                        <tr>
                            <td>{{ $daftar->id }}</td>
                            <td class="font-semibold">{{ $daftar->pasien->nama }}</td>
                            <td>{{ $daftar->keluhan ?: '-' }}</td>
                            <td>{{ $daftar->no_antrian }}</td>
                            <td>
                                <div class="flex justify-end">
                                    @if ($daftar->periksas->isNotEmpty())
                                        <span class="badge badge-success badge-outline">Sudah Diperiksa</span>
                                    @else
                                        <a href="{{ route('periksa-pasien.create', $daftar->id) }}"
                                            class="btn btn-sm btn-warning">
                                            Periksa
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-base-content/60">
                                Belum ada pasien yang mendaftar pada jadwal Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
