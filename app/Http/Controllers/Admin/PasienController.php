<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\User;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public function index()
    {
        $pasiens = User::query()
            ->where('role', 'pasien')
            ->orderBy('nama')
            ->get();

        return view('admin.pasien.index', compact('pasiens'));
    }

    public function create()
    {
        return view('admin.pasien.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'no_ktp' => ['required', 'string', 'max:16', 'unique:users,no_ktp'],
            'no_hp' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create($validated + ['role' => 'pasien']);

        return redirect()
            ->route('admin.pasien.index')
            ->with('success', 'Data pasien berhasil ditambahkan.');
    }

    public function edit(User $pasien)
    {
        abort_unless($pasien->role === 'pasien', 404);

        return view('admin.pasien.edit', compact('pasien'));
    }

    public function update(Request $request, User $pasien)
    {
        abort_unless($pasien->role === 'pasien', 404);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'no_ktp' => ['required', 'string', 'max:16', 'unique:users,no_ktp,'.$pasien->id],
            'no_hp' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$pasien->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $pasien->update($validated);

        return redirect()
            ->route('admin.pasien.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(User $pasien)
    {
        abort_unless($pasien->role === 'pasien', 404);

        if (DaftarPoli::query()->where('id_pasien', $pasien->id)->exists()) {
            return redirect()
                ->route('admin.pasien.index')
                ->with('error', 'Pasien tidak dapat dihapus karena memiliki riwayat pendaftaran.');
        }

        $pasien->delete();

        return redirect()
            ->route('admin.pasien.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }
}
