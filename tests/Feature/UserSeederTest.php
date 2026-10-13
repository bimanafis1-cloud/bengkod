<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_demo_users_and_can_be_run_again(): void
    {
        $seeder = new UserSeeder;
        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('users', 3);
        $this->assertSame(
            ['admin', 'dokter', 'pasien'],
            User::orderBy('role')->pluck('role')->all()
        );
    }
}
