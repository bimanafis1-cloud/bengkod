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

class AdminObatCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_can_view_obat_list_and_create_form(): void
    {
        Obat::create([
            'nama_obat' => 'Parasetamol',
            'kemasan' => 'Tablet',
            'harga' => 5000,
        ]);

        $this->get(route('obat.index'))
            ->assertOk()
            ->assertSeeText('Parasetamol')
            ->assertSeeText('Tablet')
            ->assertSeeText('Rp 5.000');

        $this->get(route('obat.create'))
            ->assertOk()
            ->assertSeeText('Tambah Obat');
    }

    public function test_admin_can_create_update_and_delete_obat(): void
    {
        $this->post(route('obat.store'), [
            'nama_obat' => 'Parasetamol',
            'kemasan' => 'Tablet',
            'harga' => 5000,
        ])->assertRedirect(route('obat.index'));

        $obat = Obat::where('nama_obat', 'Parasetamol')->firstOrFail();

        $this->get(route('obat.edit', $obat))
            ->assertOk()
            ->assertSee('value="Parasetamol"', false);

        $this->put(route('obat.update', $obat), [
            'nama_obat' => 'Parasetamol Forte',
            'kemasan' => null,
            'harga' => 7500,
        ])->assertRedirect(route('obat.index'));

        $this->assertDatabaseHas('obat', [
            'id' => $obat->id,
            'nama_obat' => 'Parasetamol Forte',
            'kemasan' => null,
            'harga' => 7500,
        ]);

        $this->delete(route('obat.destroy', $obat))
            ->assertRedirect(route('obat.index'));

        $this->assertDatabaseMissing('obat', ['id' => $obat->id]);
    }

    public function test_obat_fields_are_validated(): void
    {
        $this->from(route('obat.create'))
            ->post(route('obat.store'), [
                'nama_obat' => '',
                'kemasan' => str_repeat('x', 36),
                'harga' => -1,
            ])
            ->assertRedirect(route('obat.create'))
            ->assertSessionHasErrors(['nama_obat', 'kemasan', 'harga']);

        $this->assertDatabaseCount('obat', 0);
    }

    public function test_obat_used_in_prescription_cannot_be_deleted(): void
    {
        $obat = Obat::create(['nama_obat' => 'Parasetamol', 'harga' => 5000]);
        $poli = Poli::create(['nama_poli' => 'Umum']);
        $dokter = User::factory()->create(['role' => 'dokter', 'id_poli' => $poli->id]);
        $pasien = User::factory()->create(['role' => 'pasien']);
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
            'tgl_periksa' => '2026-10-29 09:00:00',
            'biaya_periksa' => 50000,
        ]);
        DetailPeriksa::create([
            'id_periksa' => $periksa->id,
            'id_obat' => $obat->id,
        ]);

        $this->delete(route('obat.destroy', $obat))
            ->assertRedirect(route('obat.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('obat', ['id' => $obat->id]);
    }
}
