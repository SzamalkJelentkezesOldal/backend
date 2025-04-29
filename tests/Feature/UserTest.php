<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase; // Automatikusan újratelepíti az adatbázist minden teszt előtt

    public function test_users_auth(): void
    {
        // Create a user with role 2 (Master/Admin)
        $admin = User::factory()->create(['email' => 'admin_auth@example.com', 'role' => 2]);

        // Acting as the admin user
        $response = $this->actingAs($admin)->getJson('/api/jelentkezok');

        // Expecting OK status because the Master user should have access
        $response->assertStatus(200);
    }

    public function test_email_egyediseg()
    {
        // Create the first user
        User::factory()->create([
            'email' => 'egyedi@example.com',
            'role' => 0,
        ]);

        // Expect an exception when trying to create a second user with the same email
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create([
            'email' => 'egyedi@example.com',
            'role' => 0,
        ]);
    }
}
