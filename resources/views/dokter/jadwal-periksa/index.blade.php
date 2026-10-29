<x-layouts.app title="Jadwal Periksa">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-primary">Dokter</p>
                <h1 class="mt-2 text-2xl font-bold text-base-content">Jadwal Periksa</h1>
                <p class="mt-1 text-base-content/70">Kelola jadwal praktik Anda.</p>
            </div>
            <a href="{{ route('jadwal-periksa.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Jadwal Periksa
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwals as $jadwal)
                        <tr>
                            <td class="font-semibold">{{ $jadwal->hari }}</td>
                            <td>{{ substr($jadwal->jam_mulai, 0, 5) }}</td>
                            <td>{{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                            <td>
                                @if ($jadwal->daftar_polis_count > 0)
                                    <span class="badge badge-warning badge-outline">Ada pendaftaran</span>
                                @else
                                    <span class="badge badge-success badge-outline">Belum ada pendaftaran</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('jadwal-periksa.edit', $jadwal) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" action="{{ route('jadwal-periksa.destroy', $jadwal) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-base-content/60">
                                <i class="fas fa-calendar-xmark mb-2 block text-2xl"></i>
                                Belum ada jadwal periksa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
