<x-layouts.app title="Data Obat">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-primary">Admin</p>
                <h1 class="mt-2 text-2xl font-bold text-base-content">Data Obat</h1>
                <p class="mt-1 text-base-content/70">Kelola daftar obat poliklinik.</p>
            </div>
            <a href="{{ route('obat.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Obat
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Kemasan</th>
                        <th>Harga</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($obats as $obat)
                        <tr>
                            <td class="font-semibold">{{ $obat->nama_obat }}</td>
                            <td>{{ $obat->kemasan ?: '-' }}</td>
                            <td>Rp {{ number_format($obat->harga, 0, ',', '.') }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('obat.edit', $obat) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('obat.destroy', $obat) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus obat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-base-content/60">
                                Belum ada data obat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
