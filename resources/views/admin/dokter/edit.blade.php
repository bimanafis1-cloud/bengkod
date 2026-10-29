<x-layouts.app title="Edit Dokter">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('admin.dokter.index') }}" class="btn btn-ghost btn-sm"
            aria-label="Kembali ke daftar dokter">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Edit Dokter</h1>
    </div>

    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        @include('admin.dokter.form', [
            'action' => route('admin.dokter.update', $dokter),
            'method' => 'PUT',
            'buttonText' => 'Update',
        ])
    </section>
</x-layouts.app>
