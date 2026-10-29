<x-layouts.app title="Tambah Poli">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('admin.polis.index') }}" class="btn btn-ghost btn-sm" aria-label="Kembali ke daftar poli">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Tambah Poli</h1>
    </div>

    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        @include('admin.polis.form', [
            'action' => route('admin.polis.store'),
            'buttonText' => 'Simpan',
        ])
    </section>
</x-layouts.app>
