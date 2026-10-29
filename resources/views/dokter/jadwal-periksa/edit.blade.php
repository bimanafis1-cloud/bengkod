<x-layouts.app title="Edit Jadwal Periksa">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('jadwal-periksa.index') }}" class="btn btn-ghost btn-sm"
            aria-label="Kembali ke daftar jadwal">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-base-content">Edit Jadwal Periksa</h1>
    </div>

    <section class="max-w-3xl rounded-2xl bg-base-100 p-6 shadow-sm">
        @include('dokter.jadwal-periksa.form', [
            'action' => route('jadwal-periksa.update', $jadwal),
            'method' => 'PUT',
            'buttonText' => 'Update',
        ])
    </section>
</x-layouts.app>
