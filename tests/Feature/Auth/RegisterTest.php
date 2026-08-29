<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_loads_without_error()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_user_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', [
            'email' => 'test-user@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_fails_with_invalid_data()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_registration_succeeds_even_if_verification_email_fails_to_send()
    {
        $this->app->bind(MailFactory::class, function () {
            return new class implements MailFactory {
                public function mailer($name = null)
                {
                    throw new RuntimeException('SMTP authentication failed');
                }
            };
        });

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'mail-failure@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', [
            'email' => 'mail-failure@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_fails_when_email_already_taken()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
