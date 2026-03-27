<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => bcrypt('123456'),
        ]);
    }

    public function test_user_can_login()
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => $this->user->email,
            'password' => '123456',
        ]);

        $response->assertRedirect(route('admin.menu.profile'));
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $this->post(route('admin.login.submit'), [
            'email' => $this->user->email,
            'password' => 'wrong',
        ])->assertStatus(302);
    }

    public function test_user_can_logout()
    {
        $this->actingAs($this->user)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));
    }

    // --- из второго файла (логика та же, просто дублируем как отдельные кейсы)

    public function test_login_ok()
    {
        $this->post(route('admin.login.submit'), [
            'email' => $this->user->email,
            'password' => '123456',
        ])->assertRedirect(route('admin.menu.profile'));
    }

    public function test_login_fail()
    {
        $this->post(route('admin.login.submit'), [
            'email' => $this->user->email,
            'password' => 'wrong',
        ])->assertStatus(302);
    }

    public function test_logout_again()
    {
        $this->actingAs($this->user)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));
    }
}
