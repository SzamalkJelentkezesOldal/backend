<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Szak; // Feltételezve, hogy van Szak modelled
use App\Models\User; // Feltételezve, hogy van User modelled
use App\Models\Jelentkezo;

class JelentkezoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new jelentkezo successfully.
     *
     * @return void
     */
    public function test_post_uj_jelentkezo_success()
    {
        // Hozz létre szakokat, amikre jelentkezni lehet
        $szak1 = Szak::factory()->create(['elnevezes' => 'Teszt Szak 1', 'portfolio' => false, 'nappali' => true]);
        $szak2 = Szak::factory()->create(['elnevezes' => 'Teszt Szak 2', 'portfolio' => false, 'nappali' => false]);

        // Correct nested structure for the request
        $data = [
            'jelentkezo' => [
                'nev' => 'Teszt Elek',
                'email' => 'teszt@example.com',
                'tel' => '123456789',
            ],
            'jelentkezes' => [
                'kivalasztottSzakok' => [$szak1->id, $szak2->id],
            ],
            // 'portfolio' => [...] // Optional portfolio data if needed
        ];

        // Email küldés mockolása, ha szükséges
        // Mail::fake();

        $response = $this->postJson('/api/uj-jelentkezo', $data);

        $response->assertStatus(201);
        // Assert database has the new Jelentkezo record
        $this->assertDatabaseHas('jelentkezos', [
            'email' => 'teszt@example.com',
            'nev' => 'Teszt Elek',
        ]);

        // Find the created Jelentkezo to check associated Jelentkezes records
        $jelentkezo = \App\Models\Jelentkezo::where('email', 'teszt@example.com')->first();
        $this->assertNotNull($jelentkezo);

        $this->assertDatabaseHas('jelentkezes', [
            'jelentkezo_id' => $jelentkezo->id,
            'szak_id' => $szak1->id,
            'sorrend' => 0, // Assuming first element gets index 0
        ]);
        $this->assertDatabaseHas('jelentkezes', [
            'jelentkezo_id' => $jelentkezo->id,
            'szak_id' => $szak2->id,
            'sorrend' => 1, // Assuming second element gets index 1
        ]);

        // Email küldés ellenőrzése, ha mockoltuk
        // Mail::assertSent(...);
    }

    /**
     * Test creating a new jelentkezo with missing data (validation error).
     *
     * @return void
     */
    public function test_post_uj_jelentkezo_validation_error()
    {
        // Adatok a várt beágyazott struktúrában
        $data = [
            'jelentkezo' => [
                'nev' => 'Teszt Elek',
                // email hiányzik
                'telefon' => '123456789',
            ],
            'jelentkezes' => [
                'kivalasztottSzakok' => [], // Vagy más szükséges jelentkezési adatok
            ],
            // Esetleges portfólió adatok...
        ];

        $response = $this->postJson('/api/uj-jelentkezo', $data);

        $response->assertStatus(422);
        // Ellenőrzi, hogy a 'jelentkezo.email' mezőre jött hiba
        $response->assertJsonValidationErrors('jelentkezo.email');
    }

    // Ide jönnek majd a többi JelentkezoControllerhez kapcsolódó tesztek
    // pl. GET /jelentkezok (Ugyintezo)
    // GET /nappali-jelentkezok, GET /esti-jelentkezok, GET /tagozat-jelentkezok/{szam} (Ugyintezo)

    // Ugyintezo Tests
    public function test_get_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_list@example.com', 'role' => 1]);
        Jelentkezo::factory()->count(5)->create();

        $response = $this->actingAs($ugyintezo)->getJson('/api/jelentkezok'); // Add pagination/filter params if needed

        $response->assertStatus(200);
        // Check structure or count based on expected response and pagination
        $response->assertJsonStructure([
            'results', 
            'totalCount'
        ]); 
        // $response->assertJsonCount(5, 'results'); // If no pagination applied
    }

    public function test_get_nappali_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_nappali@example.com', 'role' => 1]);
        // Create nappali/esti Jelentkezo/Jelentkezes data
        // ... (Requires joining Szak and checking nappali field)

        $response = $this->actingAs($ugyintezo)->getJson('/api/nappali-jelentkezok');

        $response->assertStatus(200);
        // Add assertions to check if only nappali applicants are returned
    }

    public function test_get_esti_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_esti@example.com', 'role' => 1]);
        // Create nappali/esti Jelentkezo/Jelentkezes data
        // ...

        $response = $this->actingAs($ugyintezo)->getJson('/api/esti-jelentkezok');

        $response->assertStatus(200);
        // Add assertions to check if only esti applicants are returned
    }

    public function test_get_tagozat_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_tagozat@example.com', 'role' => 1]);
        // Create nappali/esti Jelentkezo/Jelentkezes data
        // ...
        $tagozatSzam = 1; // 1 for nappali, 0 for esti based on controller logic

        $response = $this->actingAs($ugyintezo)->getJson("/api/tagozat-jelentkezok/{$tagozatSzam}");

        $response->assertStatus(200);
        // Add assertions based on $tagozatSzam
    }
} 