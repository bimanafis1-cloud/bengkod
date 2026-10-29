<x-layouts.app title="Tambah Dokter">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('admin.dokter.index') }}" class="btn btn-ghost btn-sm"
            aria-label="Kembali ke daftar dokter">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Tambah Dokter</h1>
    </div>

    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        @include('admin.dokter.form', [
            'action' => route('admin.dokter.store'),
            'buttonText' => 'Simpan',
        ])
    </section>
</x-layouts.app>
