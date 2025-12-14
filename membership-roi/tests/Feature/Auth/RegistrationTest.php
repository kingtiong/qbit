<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $inviter = User::create([
            'name' => 'Inviter',
            'email' => 'inviter@example.com',
            'password' => Hash::make('password'),
        ]);
        $inviter->forceFill(['invite_code' => 'INVITE12345'])->save();

        $response = $this->get('/register?invite=INVITE12345');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $inviter = User::create([
            'name' => 'Inviter',
            'email' => 'inviter@example.com',
            'password' => Hash::make('password'),
        ]);
        $inviter->forceFill(['invite_code' => 'INVITE12345'])->save();

        $response = $this->post('/register', [
            'invite' => 'INVITE12345',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
