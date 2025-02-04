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
    public function test_create_new_jelentkezo()
    {
        $data = [
            'jelentkezo' => [
                'nev' => 'Példa Név',
                'email' => 'pelda@example.com',
                'tel' => '123456789',
            ],
            'jelentkezes' => [
                'kivalasztottSzakok' => [1, 2] // Legalább egy szakot ki kell választani
            ]
        ];


        $response = $this->postJson('/api/uj-jelentkezo', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Sikeres jelentkezés!',
            ]);
    }
    public function test_get_jelentkezes_szama()
    {
        $szak_id = 1;  // Feltételezzük, hogy létezik egy szak 1-es ID-vel

        $response = $this->getJson("/api/szakok-szama/{$szak_id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'jelentkezes_szama'  // A válasznak tartalmaznia kell a jelentkezések számát
            ]);
    }
    public function test_torzsadat_feltoltes()
    {
        $data = [
            'address' => 'Teszt utca 123',
            'phone' => '123456789',
            // add other necessary fields
        ];

        // Feltételezzük, hogy a jelentkező ID-ja 1
        $response = $this->postJson('/api/torzsadat-feltolt', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Törzsadatok sikeresen feltöltve.',
            ]);
    }
    public function test_get_jelentkezesek_for_jelentkezo()
    {
        $jelentkezo_id = 1;  // Feltételezzük, hogy létezik egy jelentkező 1-es ID-vel

        $response = $this->getJson("/api/jelentkezesek/{$jelentkezo_id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [  // Az egyes jelentkezések adatai
                    'szak_id',
                    'allapot',
                    'datum',
                    // Add other necessary fields
                ]
            ]);
    }
}
