<?php

namespace Tests\Feature;

use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPoliCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_can_view_the_poli_list_and_create_form(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum', 'keterangan' => 'Poli umum']);

        $this->get(route('admin.polis.index'))
            ->assertOk()
            ->assertSeeText('Umum')
            ->assertSeeText('Poli umum');

        $this->get(route('admin.polis.create'))
            ->assertOk()
            ->assertSeeText('Tambah Poli');
    }

    public function test_admin_can_create_update_and_delete_a_poli(): void
    {
        $this->post(route('admin.polis.store'), [
            'nama_poli' => 'Gigi',
            'keterangan' => 'Perawatan gigi',
        ])->assertRedirect(route('admin.polis.index'));

        $poli = Poli::where('nama_poli', 'Gigi')->firstOrFail();

        $this->get(route('admin.polis.edit', $poli))
            ->assertOk()
            ->assertSeeText('Edit Poli');

        $this->put(route('admin.polis.update', $poli), [
            'nama_poli' => 'Gigi dan Mulut',
            'keterangan' => null,
        ])->assertRedirect(route('admin.polis.index'));

        $this->assertDatabaseHas('poli', [
            'id' => $poli->id,
            'nama_poli' => 'Gigi dan Mulut',
            'keterangan' => null,
        ]);

        $this->delete(route('admin.polis.destroy', $poli))
            ->assertRedirect(route('admin.polis.index'));

        $this->assertDatabaseMissing('poli', ['id' => $poli->id]);
    }

    public function test_admin_cannot_delete_a_poli_assigned_to_a_doctor(): void
    {
        $poli = Poli::create(['nama_poli' => 'Umum']);
        User::factory()->create([
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);

        $this->delete(route('admin.polis.destroy', $poli))
            ->assertRedirect(route('admin.polis.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('poli', ['id' => $poli->id]);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_poli_name_is_required_and_limited_to_the_database_column_length(): void
    {
        $this->from(route('admin.polis.create'))
            ->post(route('admin.polis.store'), [
                'nama_poli' => str_repeat('a', 26),
            ])
            ->assertRedirect(route('admin.polis.create'))
            ->assertSessionHasErrors('nama_poli');

        $this->assertDatabaseCount('poli', 0);
    }
}
