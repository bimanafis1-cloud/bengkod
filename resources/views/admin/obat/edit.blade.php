<x-layouts.app title="Edit Obat">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('obat.index') }}" class="btn btn-ghost btn-sm"
            aria-label="Kembali ke daftar obat">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Edit Obat</h1>
    </div>

    <section class="max-w-3xl rounded-2xl bg-base-100 p-6 shadow-sm">
        @include('admin.obat.form', [
            'action' => route('obat.update', $obat),
            'method' => 'PUT',
            'buttonText' => 'Update',
        ])
    </section>
</x-layouts.app>
