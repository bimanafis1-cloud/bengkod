<x-layouts.app title="Dashboard Admin">
    <section class="rounded-2xl bg-base-100 p-6 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-primary">Admin</p>
        <h1 class="mt-2 text-2xl font-bold text-base-content">Dashboard Admin</h1>
        <p class="mt-2 text-base-content/70">Selamat datang, {{ auth()->user()->nama }}.</p>
    </section>
</x-layouts.app>
