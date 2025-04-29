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
        // Create a user to be deleted and an admin user
        $userToDelete = User::factory()->create(['email' => 'delete@example.com', 'role' => 1]); // Assuming role 1 is ugyintezo
        $admin = User::factory()->create(['email' => 'admin_delete@example.com', 'role' => 2]); // Assuming role 2 is master
        $userId = $userToDelete->id;

        // Act as admin and delete the user
        $response = $this->actingAs($admin)->deleteJson("/api/delete-ugyintezo/{$userId}");
        $response->assertStatus(200);

        // Assert the user is missing from the database
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_ugyintezo_modositasa()
    {
        // Create a master user and an ugyintezo to update
        $masterUser = User::factory()->create(['email' => 'master_update@example.com', 'role' => 2]);
        $ugyintezo = User::factory()->create(['email' => 'original_update@example.com', 'role' => 1]);

        $data = [
            'name' => 'Frissített név',
            'email' => 'frissitett@teszt.com',
            // Password update might need specific handling if required
            // 'password' => 'ujjelszo',
            'role' => 1,
        ];

        // Act as master and update the ugyintezo
        $response = $this->actingAs($masterUser)
                         ->patchJson("/api/modosit-ugyintezo/{$ugyintezo->id}", $data);

        $response->assertStatus(200);

        // Assert the database has the updated user data
        $this->assertDatabaseHas('users', [
            'id'   => $ugyintezo->id,
            'name' => 'Frissített név',
            'email' => 'frissitett@teszt.com',
            'role' => 1,
        ]);
    }

    public function test_ugyintezok_listazasa()
    {
        // Create a master user to perform the action
       $masterUser = User::factory()->create(['email' => 'master_list@example.com', 'role' => 2]);

        // Create users with different roles using unique emails
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

        User::factory()->create(['email' => 'regular@example.com', 'role' => 0]); // Regular user

        // Act as master and get the list
        $response = $this->actingAs($masterUser)->getJson("/api/ugyintezok");

        $response->assertStatus(200);
        $responseData = $response->json();

        // Expecting only users with role 1 or 2
        $expectedCount = User::whereIn("role", [1, 2])->count();
        $this->assertCount($expectedCount, $responseData);

        // Verify roles in the response
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
            'role' => 1 // Assuming controller assigns role 1
        ]);
    }
} 