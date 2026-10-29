<?php

namespace Tests\Feature;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDokterCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_view_doctors_and_create_form(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);
        User::factory()->create([
            'nama' => 'Dokter Contoh',
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);

        $this->get(route('admin.dokter.index'))
            ->assertOk()
            ->assertSeeText('Dokter Contoh')
            ->assertSeeText('Umum');

        $this->get(route('admin.dokter.create'))
            ->assertOk()
            ->assertSeeText('Tambah Dokter')
            ->assertSeeText('Umum');
    }

    public function test_admin_can_create_update_and_delete_a_doctor(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);

        $this->post(route('admin.dokter.store'), [
            'nama' => 'Dokter Baru',
            'alamat' => 'Semarang',
            'no_ktp' => '1234567890123456',
            'no_hp' => '08123456789',
            'id_poli' => $poli->id,
            'email' => 'dokter@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('admin.dokter.index'));

        $dokter = User::where('email', 'dokter@example.test')->firstOrFail();
        $this->assertSame('dokter', $dokter->role);
        $this->assertTrue(password_verify('password123', $dokter->password));

        $this->get(route('admin.dokter.edit', $dokter))
            ->assertOk()
            ->assertSeeText('Edit Dokter')
            ->assertSee('value="Dokter Baru"', false);

        $this->put(route('admin.dokter.update', $dokter), [
            'nama' => 'Dokter Diperbarui',
            'alamat' => 'Kota Semarang',
            'no_ktp' => '1234567890123456',
            'no_hp' => '08123456789',
            'id_poli' => $poli->id,
            'email' => 'dokter@example.test',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.dokter.index'));

        $this->assertDatabaseHas('users', [
            'id' => $dokter->id,
            'nama' => 'Dokter Diperbarui',
            'password' => $dokter->fresh()->password,
        ]);
        $this->assertTrue(password_verify('password123', $dokter->fresh()->password));

        $this->delete(route('admin.dokter.destroy', $dokter))
            ->assertRedirect(route('admin.dokter.index'));

        $this->assertDatabaseMissing('users', ['id' => $dokter->id]);
    }

    public function test_doctor_fields_are_validated_and_duplicate_identity_is_rejected(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);
        User::factory()->create([
            'role' => 'dokter',
            'no_ktp' => '1234567890123456',
            'email' => 'existing@example.test',
        ]);

        $this->from(route('admin.dokter.create'))
            ->post(route('admin.dokter.store'), [
                'nama' => '',
                'alamat' => '',
                'no_ktp' => '1234567890123456',
                'no_hp' => str_repeat('1', 16),
                'id_poli' => 999,
                'email' => 'existing@example.test',
                'password' => '123',
                'password_confirmation' => '456',
            ])
            ->assertRedirect(route('admin.dokter.create'))
            ->assertSessionHasErrors([
                'nama',
                'alamat',
                'no_ktp',
                'no_hp',
                'id_poli',
                'email',
                'password',
            ]);

        $this->assertDatabaseCount('users', 2);
    }

    public function test_non_doctor_accounts_cannot_be_edited_or_deleted_through_doctor_routes(): void
    {
        $pasien = User::factory()->create(['role' => 'pasien']);

        $this->get(route('admin.dokter.edit', $pasien))->assertNotFound();

        $this->put(route('admin.dokter.update', $pasien), [])->assertNotFound();

        $this->delete(route('admin.dokter.destroy', $pasien))->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $pasien->id]);
    }

    public function test_doctor_with_patient_registrations_cannot_be_deleted(): void
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
        DaftarPoli::create([
            'id_pasien' => $pasien->id,
            'id_jadwal' => $jadwal->id,
            'keluhan' => 'Demam',
            'no_antrian' => 1,
        ]);

        $this->delete(route('admin.dokter.destroy', $dokter))
            ->assertRedirect(route('admin.dokter.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $dokter->id]);
        $this->assertDatabaseHas('daftar_poli', ['id_jadwal' => $jadwal->id]);
    }
}
