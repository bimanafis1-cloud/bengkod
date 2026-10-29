<?php

namespace Tests\Feature;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPasienCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_can_view_patients_and_create_form(): void
    {
        User::factory()->create([
            'nama' => 'Pasien Contoh',
            'role' => 'pasien',
        ]);

        $this->get(route('admin.pasien.index'))
            ->assertOk()
            ->assertSeeText('Pasien Contoh');

        $this->get(route('admin.pasien.create'))
            ->assertOk()
            ->assertSeeText('Tambah Pasien');
    }

    public function test_admin_can_create_update_and_delete_a_patient_without_registrations(): void
    {
        $this->post(route('admin.pasien.store'), [
            'nama' => 'Pasien Baru',
            'alamat' => 'Semarang',
            'no_ktp' => '1234567890123456',
            'no_hp' => '08123456789',
            'email' => 'pasien@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('admin.pasien.index'));

        $pasien = User::where('email', 'pasien@example.test')->firstOrFail();
        $this->assertSame('pasien', $pasien->role);
        $this->assertTrue(password_verify('password123', $pasien->password));

        $this->get(route('admin.pasien.edit', $pasien))
            ->assertOk()
            ->assertSeeText('Edit Pasien')
            ->assertSee('value="Pasien Baru"', false);

        $this->put(route('admin.pasien.update', $pasien), [
            'nama' => 'Pasien Diperbarui',
            'alamat' => 'Kota Semarang',
            'no_ktp' => '1234567890123456',
            'no_hp' => '08123456789',
            'email' => 'pasien@example.test',
            'password' => '',
        ])->assertRedirect(route('admin.pasien.index'));

        $pasien->refresh();
        $this->assertSame('Pasien Diperbarui', $pasien->nama);
        $this->assertTrue(password_verify('password123', $pasien->password));

        $this->delete(route('admin.pasien.destroy', $pasien))
            ->assertRedirect(route('admin.pasien.index'));

        $this->assertDatabaseMissing('users', ['id' => $pasien->id]);
    }

    public function test_patient_fields_are_validated_and_duplicate_identity_is_rejected(): void
    {
        User::factory()->create([
            'role' => 'pasien',
            'no_ktp' => '1234567890123456',
            'email' => 'existing@example.test',
        ]);

        $this->from(route('admin.pasien.create'))
            ->post(route('admin.pasien.store'), [
                'nama' => '',
                'alamat' => '',
                'no_ktp' => '1234567890123456',
                'no_hp' => str_repeat('1', 16),
                'email' => 'existing@example.test',
                'password' => '123',
            ])
            ->assertRedirect(route('admin.pasien.create'))
            ->assertSessionHasErrors([
                'nama',
                'alamat',
                'no_ktp',
                'no_hp',
                'email',
                'password',
            ]);

        $this->assertDatabaseCount('users', 2);
    }

    public function test_non_patient_accounts_cannot_be_edited_or_deleted_through_patient_routes(): void
    {
        $dokter = User::factory()->create(['role' => 'dokter']);

        $this->get(route('admin.pasien.edit', $dokter))->assertNotFound();
        $this->put(route('admin.pasien.update', $dokter), [])->assertNotFound();
        $this->delete(route('admin.pasien.destroy', $dokter))->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $dokter->id]);
    }

    public function test_patient_with_registration_history_cannot_be_deleted(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);
        $dokter = User::factory()->create([
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);
        $pasien = User::factory()->create(['role' => 'pasien']);
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ]);
        $pendaftaran = DaftarPoli::create([
            'id_pasien' => $pasien->id,
            'id_jadwal' => $jadwal->id,
            'keluhan' => 'Demam',
            'no_antrian' => 1,
        ]);

        $this->delete(route('admin.pasien.destroy', $pasien))
            ->assertRedirect(route('admin.pasien.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $pasien->id]);
        $this->assertDatabaseHas('daftar_poli', ['id' => $pendaftaran->id]);
    }
}
