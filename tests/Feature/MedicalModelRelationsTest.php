<?php

namespace Tests\Feature;

use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\JadwalPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_medical_models_follow_the_database_relationships(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);
        $dokter = User::factory()->create([
            'nama' => 'Dokter',
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);
        $pasien = User::factory()->create([
            'nama' => 'Pasien',
            'role' => 'pasien',
        ]);
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ]);
        $daftarPoli = DaftarPoli::create([
            'id_pasien' => $pasien->id,
            'id_jadwal' => $jadwal->id,
            'keluhan' => 'Demam',
            'no_antrian' => 1,
        ]);
        $periksa = Periksa::create([
            'id_daftar_poli' => $daftarPoli->id,
            'tgl_periksa' => '2026-10-13 09:00:00',
            'catatan' => 'Istirahat',
            'biaya_periksa' => 50000,
        ]);
        $obat = Obat::create([
            'nama_obat' => 'Parasetamol',
            'kemasan' => 'Tablet',
            'harga' => 10000,
        ]);
        $detail = DetailPeriksa::create([
            'id_periksa' => $periksa->id,
            'id_obat' => $obat->id,
        ]);

        $this->assertTrue($dokter->poli->is($poli));
        $this->assertTrue($poli->dokters->contains($dokter));
        $this->assertTrue($dokter->jadwalPeriksas->contains($jadwal));
        $this->assertTrue($jadwal->dokter->is($dokter));
        $this->assertTrue($jadwal->daftarPolis->contains($daftarPoli));
        $this->assertTrue($daftarPoli->pasien->is($pasien));
        $this->assertTrue($daftarPoli->jadwalPeriksa->is($jadwal));
        $this->assertTrue($daftarPoli->periksas->contains($periksa));
        $this->assertTrue($periksa->daftarPoli->is($daftarPoli));
        $this->assertTrue($periksa->detailPeriksas->contains($detail));
        $this->assertTrue($detail->periksa->is($periksa));
        $this->assertTrue($detail->obat->is($obat));
        $this->assertTrue($obat->detailPeriksas->contains($detail));
    }
}
