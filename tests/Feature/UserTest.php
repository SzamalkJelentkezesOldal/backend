<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_auth(): void
    {
    
        $admin = User::factory()->create(['email' => 'admin_auth@example.com', 'role' => 2]);

    
        $response = $this->actingAs($admin)->getJson('/api/jelentkezok');

    
        $response->assertStatus(200);
    }

    public function test_email_egyediseg()
    {
    
        User::factory()->create([
            'email' => 'egyedi@example.com',
            'role' => 0,
        ]);

    
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create([
            'email' => 'egyedi@example.com',
            'role' => 0,
        ]);
    }
}
