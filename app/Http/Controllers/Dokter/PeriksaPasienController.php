<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PeriksaPasienController extends Controller
{
    private const BIAYA_JASA_DOKTER = 150000;

    public function index()
    {
        $daftarPasien = DaftarPoli::query()
            ->with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', fn ($query) => $query
                ->where('id_dokter', Auth::id()))
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create(string $id)
    {
        $daftarPoli = $this->findOwnedRegistration($id);

        abort_if($daftarPoli->periksas()->exists(), 404);

        $obats = Obat::query()->orderBy('nama_obat')->get();

        return view('dokter.periksa-pasien.create', compact('daftarPoli', 'obats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_daftar_poli' => ['required', 'integer', 'exists:daftar_poli,id'],
            'obat_json' => ['required', 'json'],
            'biaya_periksa' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        $obatIds = json_decode($validated['obat_json'], true);
        Validator::make(
            ['obat_ids' => $obatIds],
            [
                'obat_ids' => ['array'],
                'obat_ids.*' => ['required', 'integer', 'distinct', 'exists:obat,id'],
            ],
        )->validate();

        DB::transaction(function () use ($validated, $obatIds): void {
            $daftarPoli = DaftarPoli::query()
                ->whereKey($validated['id_daftar_poli'])
                ->whereHas('jadwalPeriksa', fn ($query) => $query
                    ->where('id_dokter', Auth::id()))
                ->lockForUpdate()
                ->firstOrFail();

            if ($daftarPoli->periksas()->exists()) {
                throw ValidationException::withMessages([
                    'id_daftar_poli' => 'Pasien ini sudah diperiksa.',
                ]);
            }

            $obats = Obat::query()->whereKey($obatIds)->get();
            $totalHargaObat = $obats->sum('harga');
            $periksa = Periksa::create([
                'id_daftar_poli' => $daftarPoli->id,
                'tgl_periksa' => now(),
                'catatan' => $validated['catatan'] ?? null,
                'biaya_periksa' => $totalHargaObat + self::BIAYA_JASA_DOKTER,
            ]);

            $periksa->detailPeriksas()->createMany(
                $obats->map(fn (Obat $obat) => ['id_obat' => $obat->id])->all(),
            );
        });

        return redirect()
            ->route('periksa-pasien.index')
            ->with('success', 'Data pemeriksaan berhasil disimpan.');
    }

    private function findOwnedRegistration(string $id): DaftarPoli
    {
        return DaftarPoli::query()
            ->with(['pasien', 'jadwalPeriksa.dokter'])
            ->whereKey($id)
            ->whereHas('jadwalPeriksa', fn ($query) => $query
                ->where('id_dokter', Auth::id()))
            ->firstOrFail();
    }
}
