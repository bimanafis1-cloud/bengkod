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

class DokterRiwayatPasienTest extends TestCase
{
    use RefreshDatabase;

    protected User $dokter;

    protected function setUp(): void
    {
        parent::setUp();

        $poli = Poli::create(['nama_poli' => 'Umum']);
        $this->dokter = User::factory()->create([
            'nama' => 'Dr. Sari',
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);
        $this->actingAs($this->dokter);
    }

    public function test_doctor_can_view_owned_examination_history_and_details(): void
    {
        $periksa = $this->createExamination($this->dokter);
        $otherDoctor = User::factory()->create(['role' => 'dokter']);
        $otherPeriksa = $this->createExamination($otherDoctor, 'Pasien Lain');

        $this->get(route('riwayat-pasien.index'))
            ->assertOk()
            ->assertSeeText('Riwayat Pasien')
            ->assertSeeText($periksa->daftarPoli->pasien->nama)
            ->assertDontSeeText($otherPeriksa->daftarPoli->pasien->nama);

        $this->get(route('riwayat-pasien.show', $periksa))
            ->assertOk()
            ->assertSeeText('Detail Riwayat')
            ->assertSeeText($periksa->daftarPoli->pasien->nama)
            ->assertSeeText('Umum')
            ->assertSeeText('Dr. Sari')
            ->assertSeeText('Parasetamol')
            ->assertSeeText('Rp 155.000');

        $this->get(route('riwayat-pasien.show', $otherPeriksa))->assertNotFound();
    }

    public function test_only_doctors_can_access_history_routes(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'pasien']))
            ->get(route('riwayat-pasien.index'))
            ->assertForbidden();
    }

    private function createExamination(User $dokter, string $patientName = 'Budi Pasien'): Periksa
    {
        $pasien = User::factory()->create([
            'nama' => $patientName,
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
            'tgl_periksa' => '2026-10-30 09:00:00',
            'catatan' => 'Istirahat cukup',
            'biaya_periksa' => 155000,
        ]);
        $obat = Obat::create([
            'nama_obat' => 'Parasetamol',
            'kemasan' => 'Tablet',
            'harga' => 5000,
        ]);
        DetailPeriksa::create([
            'id_periksa' => $periksa->id,
            'id_obat' => $obat->id,
        ]);

        return $periksa;
    }
}
