<x-layouts.app title="Data Poli">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-primary">Admin</p>
                <h1 class="mt-2 text-2xl font-bold text-base-content">Data Poli</h1>
                <p class="mt-1 text-base-content/70">Kelola daftar poli yang tersedia di poliklinik.</p>
            </div>
            <a href="{{ route('admin.polis.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Poli
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Poli</th>
                        <th>Keterangan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($polis as $poli)
                        <tr>
                            <td class="font-semibold">{{ $poli->nama_poli }}</td>
                            <td>{{ $poli->keterangan ?: '-' }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.polis.edit', $poli) }}" class="btn btn-sm btn-outline">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.polis.destroy', $poli) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus poli ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error btn-outline">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-base-content/60">
                                Belum ada data poli.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
