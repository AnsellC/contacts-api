<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_register()
    {
        $user = User::factory()->make();
        $response = $this->postJson('/api/users', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    /**
     * @test
     * This test isn't really needed because it is testing Laravel's
     * validation rules and not the application itself but it's here
     * for sanity check.
     */
    public function a_user_cannot_register_with_invalid_credentials()
    {
        $response = $this->postJson('/api/users', [
            'name' => '',
        ]);

        $response->assertStatus(422);
    }


}
