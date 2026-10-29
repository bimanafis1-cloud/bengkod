<?php

namespace Tests\Feature;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterJadwalPeriksaCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $dokter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dokter = User::factory()->create(['role' => 'dokter']);
        $this->actingAs($this->dokter);
    }

    public function test_doctor_can_view_owned_schedules_and_create_form(): void
    {
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $this->dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ]);
        $otherDoctor = User::factory()->create(['role' => 'dokter']);
        JadwalPeriksa::create([
            'id_dokter' => $otherDoctor->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '13:00',
        ]);

        $this->get(route('jadwal-periksa.index'))
            ->assertOk()
            ->assertSeeText('Senin')
            ->assertDontSeeText('Selasa')
            ->assertSeeText('Belum ada pendaftaran');

        $this->get(route('jadwal-periksa.create'))
            ->assertOk()
            ->assertSeeText('Tambah Jadwal Periksa');

        $this->assertDatabaseHas('jadwal_periksa', ['id' => $jadwal->id]);
    }

    public function test_doctor_can_create_update_and_delete_a_schedule(): void
    {
        $this->post(route('jadwal-periksa.store'), [
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ])->assertRedirect(route('jadwal-periksa.index'));

        $jadwal = JadwalPeriksa::where('id_dokter', $this->dokter->id)->firstOrFail();

        $this->get(route('jadwal-periksa.edit', $jadwal))
            ->assertOk()
            ->assertSeeText('Edit Jadwal Periksa');

        $this->put(route('jadwal-periksa.update', $jadwal), [
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '13:00',
        ])->assertRedirect(route('jadwal-periksa.index'));

        $this->assertDatabaseHas('jadwal_periksa', [
            'id' => $jadwal->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '13:00',
        ]);

        $this->delete(route('jadwal-periksa.destroy', $jadwal))
            ->assertRedirect(route('jadwal-periksa.index'));

        $this->assertDatabaseMissing('jadwal_periksa', ['id' => $jadwal->id]);
    }

    public function test_schedule_fields_and_time_order_are_validated(): void
    {
        $this->from(route('jadwal-periksa.create'))
            ->post(route('jadwal-periksa.store'), [
                'hari' => 'Funday',
                'jam_mulai' => '12:00',
                'jam_selesai' => '11:00',
            ])
            ->assertRedirect(route('jadwal-periksa.create'))
            ->assertSessionHasErrors(['hari', 'jam_selesai']);

        $this->assertDatabaseCount('jadwal_periksa', 0);
    }

    public function test_doctor_cannot_view_or_change_another_doctors_schedule(): void
    {
        $otherDoctor = User::factory()->create(['role' => 'dokter']);
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $otherDoctor->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ]);

        $this->get(route('jadwal-periksa.edit', $jadwal))->assertNotFound();
        $this->put(route('jadwal-periksa.update', $jadwal), [])->assertNotFound();
        $this->delete(route('jadwal-periksa.destroy', $jadwal))->assertNotFound();

        $this->assertDatabaseHas('jadwal_periksa', ['id' => $jadwal->id]);
    }

    public function test_schedule_with_patient_registrations_cannot_be_deleted(): void
    {
        $pasien = User::factory()->create(['role' => 'pasien']);
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $this->dokter->id,
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

        $this->delete(route('jadwal-periksa.destroy', $jadwal))
            ->assertRedirect(route('jadwal-periksa.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('jadwal_periksa', ['id' => $jadwal->id]);
        $this->assertDatabaseHas('daftar_poli', ['id' => $daftarPoli->id]);
    }

    public function test_admin_cannot_access_doctor_schedule_routes(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('jadwal-periksa.index'))
            ->assertForbidden();
    }
}
