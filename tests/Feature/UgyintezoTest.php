<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;

class UgyintezoTest extends TestCase
{
    use RefreshDatabase;

    public function test_ugyintezo_torles(): void
    {
        
        $userToDelete = User::factory()->create(['email' => 'delete@example.com', 'role' => 1]); 
        $admin = User::factory()->create(['email' => 'admin_delete@example.com', 'role' => 2]); 
        $userId = $userToDelete->id;

        
        $response = $this->actingAs($admin)->deleteJson("/api/delete-ugyintezo/{$userId}");
        $response->assertStatus(200);

        
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_ugyintezo_modositasa()
    {
        
        $masterUser = User::factory()->create(['email' => 'master_update@example.com', 'role' => 2]);
        $ugyintezo = User::factory()->create(['email' => 'original_update@example.com', 'role' => 1]);

        $data = [
            'name' => 'Frissített név',
            'email' => 'frissitett@teszt.com',
            
            
            'role' => 1,
        ];

        
        $response = $this->actingAs($masterUser)
                         ->patchJson("/api/modosit-ugyintezo/{$ugyintezo->id}", $data);

        $response->assertStatus(200);

        
        $this->assertDatabaseHas('users', [
            'id'   => $ugyintezo->id,
            'name' => 'Frissített név',
            'email' => 'frissitett@teszt.com',
            'role' => 1,
        ]);
    }

    public function test_ugyintezok_listazasa()
    {
        
       $masterUser = User::factory()->create(['email' => 'master_list@example.com', 'role' => 2]);

        
        User::factory()
            ->count(3)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['email' => "ugyintezo{$sequence->index}@example.com", 'role' => 1],
            ))
            ->create();

        User::factory()
            ->count(2)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['email' => "master{$sequence->index}@example.com", 'role' => 2],
            ))
            ->create();

        User::factory()->create(['email' => 'regular@example.com', 'role' => 0]); 

        
        $response = $this->actingAs($masterUser)->getJson("/api/ugyintezok");

        $response->assertStatus(200);
        $responseData = $response->json();

        
        $expectedCount = User::whereIn("role", [1, 2])->count();
        $this->assertCount($expectedCount, $responseData);

        
        foreach ($responseData as $user) {
            $this->assertTrue(in_array($user["role"], [1, 2]));
        }
    }

    public function test_post_uj_ugyintezo()
    {
        $master = User::factory()->create(['email' => 'master_ugy_post@example.com', 'role' => 2]);
        $ugyintezoData = [
            'nev' => 'Új Ügyintéző',
            'email' => 'uj.ugyintezo@example.com',
            'jelszo' => 'password123',
            'jelszoMegerosites' => 'password123',
        ];

        $response = $this->actingAs($master)->postJson('/api/uj-ugyintezo', $ugyintezoData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'uj.ugyintezo@example.com',
            'name' => 'Új Ügyintéző',
            'role' => 1 
        ]);
    }
} 