<x-layouts.app title="Data Pasien">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-primary">Admin</p>
                <h1 class="mt-2 text-2xl font-bold text-base-content">Data Pasien</h1>
                <p class="mt-1 text-base-content/70">Kelola data akun dan informasi pasien.</p>
            </div>
            <a href="{{ route('admin.pasien.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Pasien
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pasien</th>
                        <th>Email</th>
                        <th>No. KTP</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pasiens as $pasien)
                        <tr>
                            <td class="font-semibold">{{ $pasien->nama }}</td>
                            <td>{{ $pasien->email }}</td>
                            <td>{{ $pasien->no_ktp ?: '-' }}</td>
                            <td>{{ $pasien->no_hp ?: '-' }}</td>
                            <td>{{ $pasien->alamat ?: '-' }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.pasien.edit', $pasien) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.pasien.destroy', $pasien) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus pasien ini?')">
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
                            <td colspan="6" class="py-8 text-center text-base-content/60">
                                Belum ada data pasien.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
