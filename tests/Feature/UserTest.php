<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_szakok(): void
    {
        $response = $this->withoutMiddleware()->get('/api/szakok');

        $response->assertStatus(200);
    }

    public function test_users_auth(): void
    {
        //$this->withoutExceptionHandling(); 
        // create rögzíti az adatbázisban a felh-t
        $admin = User::factory()->make([
            'name' => 'Teszt',
            'email' => 'teszt@teszt.com',
            'password' => 'asdasdasd',
            'role' => 2,
        ]);
        $response = $this->actingAs($admin)->get('/api/jelentkezok');
        $response->assertStatus(200);
    }
    public function test_email_must_be_unique()
    {
        // Első felhasználó létrehozása egy adott email címmel
        User::create([
            'name' => 'Első Felhasználó',
            'email' => 'egyedi@example.com',
            'password' => bcrypt('titkos1'),
            'role' => 0,
        ]);

        // Második felhasználó létrehozásakor várjuk, hogy hibát dobjon
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Próbálunk létrehozni egy újabb felhasználót ugyanazzal az email címmel
        User::create([
            'name' => 'Második Felhasználó',
            'email' => 'egyedi@example.com',
            'password' => bcrypt('titkos2'),
            'role' => 0,
        ]);
    }
    public function test_ugyintezo_deletion(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 2]); 

        $response = $this->actingAs($admin)->deleteJson("/api/delete-ugyintezo/{$user->id}");
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_ugyintezo_modositasa()
    {
        $masterUser = User::factory()->create([
            'role' => 2,
        ]);
        
        $ugyintezo = User::factory()->create([
            'name' => 'Eredeti név',
            'role' => 1,
        ]);
        
        $data = [
            'name' => 'Frissített név',
        ];
        
        $response = $this->actingAs($masterUser)
                         ->patchJson("/api/modosit-ugyintezo/" . $ugyintezo->id, $data);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('users', [
            'id'   => $ugyintezo->id,
            'name' => 'Frissített név',
            'role' => 1,
        ]);
    }

    public function test_ugyintezok_listazasa()
    {
       $masterUser = User::factory()->create([
            'role' => 2,
        ]);
        
        User::factory()->count(3)->create([
            'role' => 1,
        ]);
        
        User::factory()->count(2)->create([
            'role' => 2,
        ]);
        
        User::factory()->create([
            'role' => 0,
        ]);
        
        $response = $this->actingAs($masterUser)
                        ->getJson("/api/ugyintezok");
        
        $response->assertStatus(200);
        $responseData = $response->json();
        
        $expectedCount = User::whereIn("role", [1, 2])->count();
        $this->assertCount($expectedCount, $responseData);
        
        foreach ($responseData as $user) {
            $this->assertTrue(in_array($user["role"], [1, 2]));
        }
    }
}
