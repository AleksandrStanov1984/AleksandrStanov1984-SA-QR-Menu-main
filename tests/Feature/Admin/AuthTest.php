<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => '123456',
        ]);

        $response->assertRedirect(route('admin.menu.profile'));
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertStatus(302);
        }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));
    }
}
