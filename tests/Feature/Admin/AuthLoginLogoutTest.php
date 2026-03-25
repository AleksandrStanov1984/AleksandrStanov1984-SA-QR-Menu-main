<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthLoginLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_ok()
    {
        $u = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => $u->email,
            'password' => '123456',
        ])->assertRedirect(route('admin.menu.profile'));
    }

    public function test_login_fail()
    {
        $u = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => $u->email,
            'password' => 'wrong',
        ])->assertStatus(302); // 👈 важно (не errors!)
    }

    public function test_logout()
    {
        $u = User::factory()->create();

        $this->actingAs($u)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));
    }
}
