<x-layouts.app title="Edit Pasien">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('admin.pasien.index') }}" class="btn btn-ghost btn-sm"
            aria-label="Kembali ke daftar pasien">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Edit Pasien</h1>
    </div>

    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        @include('admin.pasien.form', [
            'action' => route('admin.pasien.update', $pasien),
            'method' => 'PUT',
            'buttonText' => 'Update',
        ])
    </section>
</x-layouts.app>
