<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'João',
            'last_name' => 'Silva',
            'nickname' => 'joaozinho',
            'email' => 'joao@example.com',
            'phone' => '(42) 91234-5678',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
        ], $overrides);
    }

    private function register(array $overrides = [])
    {
        return $this->post(route('register.store'), $this->validData($overrides));
    }

    public function test_register_page_loads_with_all_fields(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="last_name"', false)
            ->assertSee('name="nickname"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('data-password-toggle="password"', false)
            ->assertSee('data-password-toggle="password_confirmation"', false);
    }

    public function test_authenticated_user_cannot_see_register_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('register'))
            ->assertRedirect();
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $this->register()->assertRedirect(route('inicio'));

        $this->assertDatabaseHas('users', ['email' => 'joao@example.com']);
        $this->assertAuthenticated();
    }

    public function test_all_fields_are_stored(): void
    {
        $this->register();

        $this->assertDatabaseHas('users', [
            'name' => 'João',
            'last_name' => 'Silva',
            'nickname' => 'joaozinho',
            'email' => 'joao@example.com',
            'phone' => '42912345678',
        ]);
    }

    public function test_registered_event_is_dispatched(): void
    {
        Event::fake([Registered::class]);

        $this->register();

        Event::assertDispatched(Registered::class);
    }

    public function test_user_gets_uuid_and_is_active_by_default(): void
    {
        $this->register();

        $user = User::where('email', 'joao@example.com')->first();

        $this->assertTrue(Str::isUuid($user->id));
        $this->assertTrue($user->active);
    }

    public function test_active_cannot_be_set_through_the_form(): void
    {
        $this->register(['active' => false]);

        $this->assertTrue(User::where('email', 'joao@example.com')->first()->active);
    }

    public function test_password_is_stored_hashed(): void
    {
        $this->register();

        $user = User::where('email', 'joao@example.com')->first();

        $this->assertNotSame('Senha@123', $user->password);
        $this->assertTrue(Hash::check('Senha@123', $user->password));
    }

    public function test_email_is_stored_lowercase(): void
    {
        $this->register(['email' => 'JOAO@Example.com']);

        $this->assertDatabaseHas('users', ['email' => 'joao@example.com']);
    }

    public function test_phone_is_stored_with_digits_only(): void
    {
        $this->register(['phone' => '(42) 91234-5678']);

        $this->assertDatabaseHas('users', ['phone' => '42912345678']);
    }

    public function test_phone_without_mask_is_accepted(): void
    {
        $this->register(['phone' => '42912345678'])
            ->assertRedirect(route('inicio'));
    }

    public function test_required_fields_are_validated(): void
    {
        foreach (['name', 'last_name', 'email', 'phone', 'password'] as $field) {
            $this->register([$field => ''])
                ->assertSessionHasErrors($field);

            $this->assertGuest();
        }
    }

    public function test_nickname_is_optional(): void
    {
        $this->register(['nickname' => ''])
            ->assertRedirect(route('inicio'));

        $this->assertDatabaseHas('users', ['email' => 'joao@example.com', 'nickname' => null]);
    }

    public function test_max_lengths_are_enforced(): void
    {
        $this->register(['name' => str_repeat('a', 33)])->assertSessionHasErrors('name');
        $this->register(['last_name' => str_repeat('a', 51)])->assertSessionHasErrors('last_name');
        $this->register(['nickname' => str_repeat('a', 33)])->assertSessionHasErrors('nickname');
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->register(['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $this->register(['phone' => '(42) 98888-7777'])
            ->assertSessionHasErrors('email');
    }

    public function test_duplicate_email_with_different_case_is_rejected(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $this->register(['email' => 'JOAO@example.com', 'phone' => '(42) 98888-7777'])
            ->assertSessionHasErrors('email');
    }

    public function test_invalid_phone_is_rejected(): void
    {
        foreach (['123', '4291234567', '429123456789', 'abcdefghijk'] as $invalid) {
            $this->register(['phone' => $invalid])
                ->assertSessionHasErrors('phone');
        }
    }

    public function test_duplicate_phone_is_rejected(): void
    {
        User::factory()->create(['phone' => '42912345678']);

        $this->register(['email' => 'outro@example.com'])
            ->assertSessionHasErrors('phone');
    }

    public function test_duplicate_phone_is_rejected_even_with_a_different_mask(): void
    {
        User::factory()->create(['phone' => '42912345678']);

        $this->register(['email' => 'outro@example.com', 'phone' => '42 91234 5678'])
            ->assertSessionHasErrors('phone');
    }

    public function test_password_confirmation_must_match(): void
    {
        $this->register(['password_confirmation' => 'Different@123'])
            ->assertSessionHasErrors('password');
    }

    public function test_short_password_is_rejected(): void
    {
        $this->register(['password' => 'Ab@1', 'password_confirmation' => 'Ab@1'])
            ->assertSessionHasErrors('password');
    }

    public function test_password_requires_uppercase_number_and_symbol(): void
    {
        foreach (['senha@123', 'Senha@abc', 'Senha1234'] as $weak) {
            $this->register(['password' => $weak, 'password_confirmation' => $weak])
                ->assertSessionHasErrors('password');
        }
    }

    public function test_old_input_is_kept_but_password_is_not(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), $this->validData(['email' => 'not-an-email']))
            ->assertRedirect(route('register'))
            ->assertSessionHasInput('name', 'João')
            ->assertSessionHasInput('last_name', 'Silva')
            ->assertSessionHasInput('phone', '(42) 91234-5678')
            ->assertSessionMissing('_old_input.password');
    }
}
