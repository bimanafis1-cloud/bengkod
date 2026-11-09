<?php

namespace Tests\Feature;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterPeriksaPasienTest extends TestCase
{
    use RefreshDatabase;

    protected User $dokter;

    protected function setUp(): void
    {
        parent::setUp();

        $poli = Poli::create(['nama_poli' => 'Umum']);
        $this->dokter = User::factory()->create([
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);
        $this->actingAs($this->dokter);
    }

    public function test_doctor_can_view_owned_patient_queue_and_examination_form(): void
    {
        $daftarPoli = $this->createRegistration($this->dokter);
        $otherDoctor = User::factory()->create(['role' => 'dokter']);
        $otherRegistration = $this->createRegistration($otherDoctor, 'Pasien Lain');

        $this->get(route('periksa-pasien.index'))
            ->assertOk()
            ->assertSeeText($daftarPoli->pasien->nama)
            ->assertDontSeeText($otherRegistration->pasien->nama);

        $this->get(route('periksa-pasien.create', $daftarPoli->id))
            ->assertOk()
            ->assertSeeText($daftarPoli->pasien->nama)
            ->assertSeeText('Pilih Obat');

        $this->get(route('periksa-pasien.create', $otherRegistration->id))
            ->assertNotFound();
    }

    public function test_doctor_can_examine_owned_patient_and_server_calculates_total(): void
    {
        $daftarPoli = $this->createRegistration($this->dokter);
        $obatA = Obat::create(['nama_obat' => 'Parasetamol', 'harga' => 5000]);
        $obatB = Obat::create(['nama_obat' => 'Vitamin', 'harga' => 10000]);

        $this->post(route('periksa-pasien.store'), [
            'id_daftar_poli' => $daftarPoli->id,
            'obat_json' => json_encode([$obatA->id, $obatB->id]),
            'biaya_periksa' => 1,
            'catatan' => 'Istirahat cukup',
        ])->assertRedirect(route('periksa-pasien.index'));

        $periksa = Periksa::where('id_daftar_poli', $daftarPoli->id)->firstOrFail();
        $this->assertSame(165000, $periksa->biaya_periksa);
        $this->assertSame('Istirahat cukup', $periksa->catatan);
        $this->assertDatabaseCount('detail_periksa', 2);
        $this->assertDatabaseHas('detail_periksa', [
            'id_periksa' => $periksa->id,
            'id_obat' => $obatA->id,
        ]);

        $this->get(route('periksa-pasien.index'))
            ->assertOk()
            ->assertSeeText('Sudah Diperiksa');
        $this->get(route('periksa-pasien.create', $daftarPoli->id))->assertNotFound();
    }

    public function test_doctor_cannot_examine_another_doctors_patient_or_unknown_medicine(): void
    {
        $otherDoctor = User::factory()->create(['role' => 'dokter']);
        $otherRegistration = $this->createRegistration($otherDoctor);
        $ownRegistration = $this->createRegistration($this->dokter, 'Pasien Sendiri');

        $this->post(route('periksa-pasien.store'), [
            'id_daftar_poli' => $otherRegistration->id,
            'obat_json' => '[]',
            'biaya_periksa' => 0,
        ])->assertNotFound();

        $this->from(route('periksa-pasien.create', $ownRegistration->id))
            ->post(route('periksa-pasien.store'), [
                'id_daftar_poli' => $ownRegistration->id,
                'obat_json' => '[99999]',
                'biaya_periksa' => 0,
            ])
            ->assertRedirect(route('periksa-pasien.create', $ownRegistration->id))
            ->assertSessionHasErrors(['obat_ids.0']);

        $this->assertDatabaseCount('periksa', 0);
        $this->assertDatabaseCount('detail_periksa', 0);
    }

    public function test_only_doctors_can_access_patient_examination_routes(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'pasien']))
            ->get(route('periksa-pasien.index'))
            ->assertForbidden();
    }

    private function createRegistration(User $dokter, string $patientName = 'Pasien'): DaftarPoli
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

        return DaftarPoli::create([
            'id_pasien' => $pasien->id,
            'id_jadwal' => $jadwal->id,
            'keluhan' => 'Demam',
            'no_antrian' => 1,
        ]);
    }
}
