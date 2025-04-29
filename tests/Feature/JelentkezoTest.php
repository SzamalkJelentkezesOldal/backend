<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Szak; 
use App\Models\User; 
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
        
        $szak1 = Szak::factory()->create(['elnevezes' => 'Teszt Szak 1', 'portfolio' => false, 'nappali' => true]);
        $szak2 = Szak::factory()->create(['elnevezes' => 'Teszt Szak 2', 'portfolio' => false, 'nappali' => false]);

        
        $data = [
            'jelentkezo' => [
                'nev' => 'Teszt Elek',
                'email' => 'teszt@example.com',
                'tel' => '123456789',
            ],
            'jelentkezes' => [
                'kivalasztottSzakok' => [$szak1->id, $szak2->id],
            ],
            
        ];

        
        

        $response = $this->postJson('/api/uj-jelentkezo', $data);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('jelentkezos', [
            'email' => 'teszt@example.com',
            'nev' => 'Teszt Elek',
        ]);

        
        $jelentkezo = \App\Models\Jelentkezo::where('email', 'teszt@example.com')->first();
        $this->assertNotNull($jelentkezo);

        $this->assertDatabaseHas('jelentkezes', [
            'jelentkezo_id' => $jelentkezo->id,
            'szak_id' => $szak1->id,
            'sorrend' => 0, 
        ]);
        $this->assertDatabaseHas('jelentkezes', [
            'jelentkezo_id' => $jelentkezo->id,
            'szak_id' => $szak2->id,
            'sorrend' => 1, 
        ]);

        
        
    }

    /**
     * Test creating a new jelentkezo with missing data (validation error).
     *
     * @return void
     */
    public function test_post_uj_jelentkezo_validation_error()
    {
        
        $data = [
            'jelentkezo' => [
                'nev' => 'Teszt Elek',
                
                'telefon' => '123456789',
            ],
            'jelentkezes' => [
                'kivalasztottSzakok' => [], 
            ],
            
        ];

        $response = $this->postJson('/api/uj-jelentkezo', $data);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrors('jelentkezo.email');
    }

    
    
    

    
    public function test_get_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_list@example.com', 'role' => 1]);
        Jelentkezo::factory()->count(5)->create();

        $response = $this->actingAs($ugyintezo)->getJson('/api/jelentkezok'); 

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'results', 
            'totalCount'
        ]); 
        
    }

    public function test_get_nappali_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_nappali@example.com', 'role' => 1]);
        
        

        $response = $this->actingAs($ugyintezo)->getJson('/api/nappali-jelentkezok');

        $response->assertStatus(200);
        
    }

    public function test_get_esti_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_esti@example.com', 'role' => 1]);
        
        

        $response = $this->actingAs($ugyintezo)->getJson('/api/esti-jelentkezok');

        $response->assertStatus(200);
        
    }

    public function test_get_tagozat_jelentkezok()
    {
        $ugyintezo = User::factory()->create(['email' => 'jel_tagozat@example.com', 'role' => 1]);
        
        
        $tagozatSzam = 1; 

        $response = $this->actingAs($ugyintezo)->getJson("/api/tagozat-jelentkezok/{$tagozatSzam}");

        $response->assertStatus(200);
        
    }
} 