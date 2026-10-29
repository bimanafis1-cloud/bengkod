<x-layouts.app title="Data Dokter">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-primary">Admin</p>
                <h1 class="mt-2 text-2xl font-bold text-base-content">Data Dokter</h1>
                <p class="mt-1 text-base-content/70">Kelola data dokter dan poli tempat praktik.</p>
            </div>
            <a href="{{ route('admin.dokter.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Dokter
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Dokter</th>
                        <th>Email</th>
                        <th>No. KTP</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Poli</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dokters as $dokter)
                        <tr>
                            <td class="font-semibold">{{ $dokter->nama }}</td>
                            <td>{{ $dokter->email }}</td>
                            <td>{{ $dokter->no_ktp }}</td>
                            <td>{{ $dokter->no_hp }}</td>
                            <td>{{ $dokter->alamat }}</td>
                            <td>
                                @if ($dokter->poli)
                                    <span class="badge badge-primary badge-outline">{{ $dokter->poli->nama_poli }}</span>
                                @else
                                    <span class="text-base-content/60">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.dokter.edit', $dokter) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.dokter.destroy', $dokter) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus dokter ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-base-content/60">
                                Belum ada data dokter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
