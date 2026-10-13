<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_render(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk ke akun Anda');

        $this->get('/register')
            ->assertOk()
            ->assertSee('Buat akun baru');
    }

    public function test_each_role_can_render_its_dashboard(): void
    {
        foreach ([
            'admin' => '/admin/dashboard',
            'dokter' => '/dokter/dashboard',
            'pasien' => '/pasien/dashboard',
        ] as $role => $path) {
            $user = new User;
            $user->nama = ucfirst($role);
            $user->role = $role;

            $this->actingAs($user)
                ->get($path)
                ->assertOk()
                ->assertSee('Dashboard '.ucfirst($role));
        }
    }

    public function test_patient_can_register_with_matching_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Bima Pasien',
            'alamat' => 'Jl. Sehat No. 1',
            'no_ktp' => '1234567890123456',
            'no_hp' => '081234567890',
            'email' => 'bima@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $user = User::where('email', 'bima@example.test')->firstOrFail();

        $this->assertSame('Bima Pasien', $user->nama);
        $this->assertSame('pasien', $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_login_redirects_user_to_the_dashboard_for_their_role(): void
    {
        User::factory()->create([
            'email' => 'pasien@example.test',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => 'pasien@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('pasien.dashboard'));
    }
}
