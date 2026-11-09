<?php

namespace Tests\Feature;

use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasienPendaftaranPoliTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_view_registration_form_and_register_for_a_schedule(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);
        $dokter = User::factory()->create(['role' => 'dokter', 'id_poli' => $poli->id]);
        $pasien = User::factory()->create(['role' => 'pasien', 'no_rm' => 'RM-001']);
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ]);

        $this->actingAs($pasien)
            ->get(route('pasien.daftar'))
            ->assertOk()
            ->assertSeeText('Pendaftaran Poli')
            ->assertSeeText('Umum')
            ->assertSeeText('Senin');

        $this->post(route('pasien.daftar.submit'), [
            'id_poli' => $poli->id,
            'id_jadwal' => $jadwal->id,
            'id_pasien' => User::factory()->create(['role' => 'pasien'])->id,
            'keluhan' => 'Demam',
        ])->assertRedirect(route('pasien.daftar'));

        $this->assertDatabaseHas('daftar_poli', [
            'id_pasien' => $pasien->id,
            'id_jadwal' => $jadwal->id,
            'keluhan' => 'Demam',
            'no_antrian' => 1,
        ]);
        $this->assertDatabaseCount('daftar_poli', 1);
    }

    public function test_patient_cannot_register_for_a_schedule_outside_the_selected_poli(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);
        $otherPoli = Poli::create(['nama_poli' => 'Gigi']);
        $dokter = User::factory()->create(['role' => 'dokter', 'id_poli' => $otherPoli->id]);
        $pasien = User::factory()->create(['role' => 'pasien']);
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ]);

        $this->actingAs($pasien)
            ->from(route('pasien.daftar'))
            ->post(route('pasien.daftar.submit'), [
                'id_poli' => $poli->id,
                'id_jadwal' => $jadwal->id,
                'keluhan' => 'Sakit gigi',
            ])
            ->assertRedirect(route('pasien.daftar'))
            ->assertSessionHasErrors(['id_jadwal']);

        $this->assertDatabaseCount('daftar_poli', 0);
    }

    public function test_only_patients_can_access_poli_registration(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'dokter']))
            ->get(route('pasien.daftar'))
            ->assertForbidden();
    }
}
