<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PoliController extends Controller
{
    public function get()
    {
        $polis = Poli::query()->orderBy('nama_poli')->get();
        $jadwals = JadwalPeriksa::query()
            ->with(['dokter.poli'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('pasien.daftar', [
            'user' => Auth::user(),
            'polis' => $polis,
            'jadwals' => $jadwals,
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'id_poli' => ['required', 'integer', 'exists:poli,id'],
            'id_jadwal' => ['required', 'integer', 'exists:jadwal_periksa,id'],
            'keluhan' => ['nullable', 'string', 'max:5000'],
        ]);

        $jadwal = JadwalPeriksa::query()
            ->whereKey($validated['id_jadwal'])
            ->whereHas('dokter', fn ($query) => $query
                ->where('role', 'dokter')
                ->where('id_poli', $validated['id_poli']))
            ->first();

        if (! $jadwal) {
            throw ValidationException::withMessages([
                'id_jadwal' => 'Jadwal tidak tersedia untuk poli yang dipilih.',
            ]);
        }

        DB::transaction(function () use ($validated, $jadwal): void {
            $lockedJadwal = JadwalPeriksa::query()
                ->whereKey($jadwal->id)
                ->lockForUpdate()
                ->firstOrFail();
            $nomorAntrian = DaftarPoli::query()
                ->where('id_jadwal', $lockedJadwal->id)
                ->count() + 1;

            DaftarPoli::create([
                'id_pasien' => Auth::id(),
                'id_jadwal' => $lockedJadwal->id,
                'keluhan' => $validated['keluhan'] ?? '',
                'no_antrian' => $nomorAntrian,
            ]);
        });

        return redirect()
            ->route('pasien.daftar')
            ->with('success', 'Berhasil mendaftar ke poli.');
    }
}
