<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Szak; // Feltételezve, hogy van Szak modelled
use App\Models\Jelentkezo;
use App\Models\Jelentkezes;
use App\Models\User;

class SzakTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching all szakok.
     *
     * @return void
     */
    public function test_get_szakok_returns_list()
    {
        // Opcionális: Hozz létre néhány szakot a teszteléshez
        Szak::factory()->count(3)->create(['elnevezes' => 'Teszt Szak', 'portfolio' => false, 'nappali' => true]);

        $response = $this->get('/api/szakok');

        $response->assertStatus(200);
        // Opcionális: Ellenőrizd a JSON struktúrát vagy a darabszámot
        // $response->assertJsonCount(3);
    }

    public function test_szakok_from_user_test(): void
    {
        // This test was moved from UserTest.php
        // It might need adjustments depending on Auth requirements
        $response = $this->get('/api/szakok');
        $response->assertStatus(200);
    }

    // Ugyintezo Tests

    public function test_get_szakra_jelentkezett()
    {
        $ugyintezo = User::factory()->create(['email' => 'szakra_jel@example.com', 'role' => 1]);
        $szak = Szak::factory()->create(['elnevezes' => 'Szak X', 'portfolio' => false, 'nappali' => true]);
        $jelentkezo1 = Jelentkezo::factory()->create();
        $jelentkezo2 = Jelentkezo::factory()->create();
        Jelentkezes::factory()->create(['jelentkezo_id' => $jelentkezo1->id, 'szak_id' => $szak->id, 'allapot' => 1]);
        Jelentkezes::factory()->create(['jelentkezo_id' => $jelentkezo2->id, 'szak_id' => $szak->id, 'allapot' => 1]);

        $response = $this->actingAs($ugyintezo)->getJson("/api/szakra-jelentkezett/{$szak->elnevezes}");

        $response->assertStatus(200);
        $response->assertJsonCount(2); // Expecting 2 applicants for this szak
        // Add more specific assertions if needed
    }

    public function test_get_jelentkezok_szama_szakra()
    {
        $ugyintezo = User::factory()->create(['email' => 'szakra_jel_count@example.com', 'role' => 1]);
        $szak = Szak::factory()->create(['elnevezes' => 'Szak Y', 'portfolio' => false, 'nappali' => true]);
        Jelentkezes::factory()->count(3)->create([
            'szak_id' => $szak->id,
            'jelentkezo_id' => Jelentkezo::factory(), // Create Jelentkezo on the fly
            'allapot' => 1
        ]);

        $response = $this->actingAs($ugyintezo)->getJson("/api/jelentkezok-szama/{$szak->id}");

        $response->assertStatus(200);
        $response->assertJson(['count' => 3]); // Assuming the response has a 'count' key
    }

    // Statistics Tests (Ugyintezo)
    public function test_get_jelentkezok_szama_statisztika()
    {
        $ugyintezo = User::factory()->create(['email' => 'stat_count@example.com', 'role' => 1]);
        // Setup data for statistics
        // ...

        $response = $this->actingAs($ugyintezo)->getJson('/api/jelentkezok-szama-statisztika');
        $response->assertStatus(200);
        // Add assertions
    }

    public function test_get_jelentkezok_tagozatra_szakra_bontva()
    {
        $ugyintezo = User::factory()->create(['email' => 'stat_tag_szak@example.com', 'role' => 1]);
        // Setup data for statistics
        // ...

        $response = $this->actingAs($ugyintezo)->getJson('/api/jelentkezok-tagozatra-szakra-bontva');
        $response->assertStatus(200);
        // Add assertions
    }

    public function test_get_jelentkezok_tagozatra_bontva()
    {
        $ugyintezo = User::factory()->create(['email' => 'stat_tag@example.com', 'role' => 1]);
        // Setup data for statistics
        // ...

        $response = $this->actingAs($ugyintezo)->getJson('/api/jelentkezok-tagozatra-bontva');
        $response->assertStatus(200);
        // Add assertions
    }

    // Master Tests
    public function test_post_uj_szak()
    {
        $master = User::factory()->create(['email' => 'master_szak_post@example.com', 'role' => 2]);
        $szakData = [
            'elnevezes' => 'Új Mester Szak',
            'portfolio' => false,
            'nappali' => true,
            // Add other required fields based on Szak model/migration
        ];

        $response = $this->actingAs($master)->postJson('/api/uj-szak', $szakData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('szaks', ['elnevezes' => 'Új Mester Szak']);
    }

    public function test_patch_modosit_szak()
    {
        $master = User::factory()->create(['email' => 'master_szak_patch@example.com', 'role' => 2]);
        $szak = Szak::factory()->create(['elnevezes' => 'Régi Szak', 'portfolio' => false, 'nappali' => true]);
        $updateData = [
            'elnevezes' => 'Frissített Szak',
            'nappali' => false,
        ];

        $response = $this->actingAs($master)->patchJson("/api/modosit-szak/{$szak->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('szaks', ['id' => $szak->id, 'elnevezes' => 'Frissített Szak', 'nappali' => false]);
    }

    public function test_delete_szak()
    {
        $master = User::factory()->create(['email' => 'master_szak_delete@example.com', 'role' => 2]);
        $szak = Szak::factory()->create(['elnevezes' => 'Törlendő Szak', 'portfolio' => false, 'nappali' => true]);

        $response = $this->actingAs($master)->deleteJson("/api/delete-szak/{$szak->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('szaks', ['id' => $szak->id]);
    }

    // Ide jönnek majd a többi SzakControllerhez kapcsolódó tesztek
    // pl. GET /szakra-jelentkezett/{szak}, GET /jelentkezok-szama/{szak}, stb.
    // POST /uj-szak, PATCH /modosit-szak/{id}, DELETE /delete-szak/{id} (Master jogosultsággal)
    // GET /jelentkezok-szama-statisztika, GET /jelentkezok-tagozatra-szakra-bontva, GET /jelentkezok-tagozatra-bontva (Ugyintezo jogosultsággal)
} 