<x-layouts.app title="Edit Poli">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('admin.polis.index') }}" class="btn btn-ghost btn-sm" aria-label="Kembali ke daftar poli">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Edit Poli</h1>
    </div>

    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        @include('admin.polis.form', [
            'action' => route('admin.polis.update', $poli),
            'method' => 'PUT',
            'buttonText' => 'Simpan',
        ])
    </section>
</x-layouts.app>
