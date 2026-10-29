<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    private const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $jadwals = JadwalPeriksa::query()
            ->where('id_dokter', Auth::id())
            ->withCount('daftarPolis')
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END")
            ->orderBy('jam_mulai')
            ->get();

        return view('dokter.jadwal-periksa.index', compact('jadwals'));
    }

    public function create()
    {
        return view('dokter.jadwal-periksa.create', ['hariList' => self::HARI]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => ['required', 'string', 'in:'.implode(',', self::HARI)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        Auth::user()->jadwalPeriksas()->create($validated);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil ditambahkan.');
    }

    public function edit(JadwalPeriksa $jadwalPeriksa)
    {
        $this->authorizeOwnership($jadwalPeriksa);

        return view('dokter.jadwal-periksa.edit', [
            'jadwal' => $jadwalPeriksa,
            'hariList' => self::HARI,
        ]);
    }

    public function update(Request $request, JadwalPeriksa $jadwalPeriksa)
    {
        $this->authorizeOwnership($jadwalPeriksa);

        $validated = $request->validate([
            'hari' => ['required', 'string', 'in:'.implode(',', self::HARI)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $jadwalPeriksa->update($validated);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil diperbarui.');
    }

    public function destroy(JadwalPeriksa $jadwalPeriksa)
    {
        $this->authorizeOwnership($jadwalPeriksa);

        if ($jadwalPeriksa->daftarPolis()->exists()) {
            return redirect()
                ->route('jadwal-periksa.index')
                ->with('error', 'Jadwal tidak dapat dihapus karena sudah memiliki pendaftaran pasien.');
        }

        $jadwalPeriksa->delete();

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil dihapus.');
    }

    private function authorizeOwnership(JadwalPeriksa $jadwalPeriksa): void
    {
        abort_unless($jadwalPeriksa->id_dokter === Auth::id(), 404);
    }
}
