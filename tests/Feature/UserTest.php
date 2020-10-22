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

    /**
     * @test
     */
    public function a_user_can_login()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function an_invalid_user_cannot_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'something',
            'password' => 'password1'
        ]);
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function a_user_can_get_his_credentials()
    {
        $user = User::factory()->create();


        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $response->json('token');
        $this->assertNotNull($token);

        $response = $this->postJson('/api/me', [], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200);
        $this->assertEquals($user->toArray(), $response->json());
    }

}
