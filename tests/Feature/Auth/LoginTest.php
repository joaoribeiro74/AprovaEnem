<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function attemptLogin(string $email, string $password)
    {
        return $this->from(route('login'))->post(route('login.store'), [
            'email'    => $email,
            'password' => $password,
        ]);
    }

    public function test_login_page_loads_with_password_toggle(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('id="password"', false)
            ->assertSee('id="toggle-password"', false);
    }

    public function test_empty_fields_fail_validation(): void
    {
        $this->attemptLogin('', '')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_invalid_email_format_fails_validation(): void
    {
        $this->attemptLogin('not-an-email', 'password')
            ->assertSessionHasErrors('email');
    }

    public function test_user_logs_in_with_correct_credentials(): void
    {
        $user = User::factory()->create();

        $this->attemptLogin($user->email, 'password')
            ->assertRedirect(route('inicio'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_shows_error_and_keeps_email(): void
    {
        $user = User::factory()->create();

        $this->attemptLogin($user->email, 'wrong-password')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => __('auth.failed')])
            ->assertSessionHasInput('email', $user->email);

        $this->assertGuest();
    }

    public function test_unknown_email_shows_the_same_error(): void
    {
        $this->attemptLogin('nobody@example.com', 'password')
            ->assertSessionHasErrors(['email' => __('auth.failed')]);

        $this->assertGuest();
    }

    public function test_session_id_is_regenerated_on_login(): void
    {
        $user = User::factory()->create();

        $this->startSession();
        $oldId = session()->getId();

        $this->attemptLogin($user->email, 'password');

        $this->assertNotSame($oldId, session()->getId());
    }

    public function test_sixth_attempt_is_blocked_with_countdown(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->attemptLogin($user->email, 'wrong-password')
                ->assertSessionHasErrors(['email' => __('auth.failed')]);
        }

        $response = $this->attemptLogin($user->email, 'wrong-password');

        $response->assertSessionHasErrors('email');
        $response->assertSessionHas('retry_after');

        $this->assertSame(
            __('auth.throttle', ['seconds' => session('retry_after')]),
            session('errors')->first('email')
        );
    }

    public function test_correct_password_is_also_blocked_while_limited(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->attemptLogin($user->email, 'wrong-password');
        }

        $this->attemptLogin($user->email, 'password')
            ->assertSessionHas('retry_after');

        $this->assertGuest();
    }

    public function test_block_only_affects_the_email_that_hit_the_limit(): void
    {
        $blocked = User::factory()->create();
        $other   = User::factory()->create();

        for ($i = 0; $i < 6; $i++) {
            $this->attemptLogin($blocked->email, 'wrong-password');
        }

        $this->attemptLogin($other->email, 'password')
            ->assertRedirect(route('inicio'));

        $this->assertAuthenticatedAs($other);
    }

    public function test_email_case_does_not_bypass_the_limit(): void
    {
        $user = User::factory()->create(['email' => 'joao@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->attemptLogin('JOAO@example.com', 'wrong-password');
        }

        $this->attemptLogin('joao@example.com', 'wrong-password')
            ->assertSessionHas('retry_after');
    }

    public function test_successful_login_resets_the_counter(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 4; $i++) {
            $this->attemptLogin($user->email, 'wrong-password');
        }

        $this->attemptLogin($user->email, 'password');
        $this->post(route('logout'));

        for ($i = 0; $i < 4; $i++) {
            $this->attemptLogin($user->email, 'wrong-password');
        }

        $this->attemptLogin($user->email, 'password')
            ->assertRedirect(route('inicio'));
    }

    public function test_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
