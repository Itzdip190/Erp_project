<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    /** @test */
    public function forgot_password_link_request_returns_success_and_token()
    {
        $user = User::factory()->create([
            'email' => 'testuser@yis.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->postJson(route('password.email'), [
            'login_input' => 'testuser@yis.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'testuser@yis.com',
        ]);
    }

    /** @test */
    public function user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'resetuser@yis.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = 'test-token-123456';
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'resetuser@yis.com'],
            [
                'email' => 'resetuser@yis.com',
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'resetuser@yis.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
