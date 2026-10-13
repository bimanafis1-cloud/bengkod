<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('role:admin')->get('/_tests/role/admin', fn () => response('ok'));
    }

    public function test_user_with_the_required_role_can_access_the_route(): void
    {
        $user = new User;
        $user->role = 'admin';

        $this->actingAs($user)
            ->get('/_tests/role/admin')
            ->assertOk()
            ->assertSeeText('ok');
    }

    public function test_user_with_a_different_role_is_forbidden(): void
    {
        $user = new User;
        $user->role = 'pasien';

        $this->actingAs($user)
            ->get('/_tests/role/admin')
            ->assertForbidden();
    }

    public function test_guest_is_unauthorized(): void
    {
        $this->get('/_tests/role/admin')
            ->assertUnauthorized();
    }
}
