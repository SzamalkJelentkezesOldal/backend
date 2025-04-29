<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Jelentkezo;
use App\Models\JelentkezoTorzs;

class JelentkezoTorzsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_jelentkezo_adatai()
    {
        $jelentkezo = Jelentkezo::factory()->create(['email' => 'getadatok@example.com']);
        JelentkezoTorzs::factory()->create([
            'jelentkezo_id' => $jelentkezo->id,
            'vezeteknev' => 'Teszt',
            'keresztnev' => 'Elek',
            'szuletesi_nev' => 'Születési Teszt Elek',
            'anyja_neve' => 'Anyja neve',
            'szuletesi_datum' => '2000-01-01',
            'szuletesi_hely' => 'Budapest',
            'allampolgarsag' => 'Magyar',
            'lakcim' => '1111 Budapest, Teszt utca 1.',
            'taj_szam' => '123456789',
        ]);

        $response = $this->getJson("/api/jelentkezo-adatai/{$jelentkezo->email}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'jelentkezo_id', 'vezeteknev', 'keresztnev', 'szuletesi_nev', 'szuletesi_datum'
        ]);
    }

    public function test_post_torzsadat_feltolt()
    {
        $user = User::factory()->create(['email' => 'torzs_feltolt@example.com', 'role' => 0]);
        $jelentkezo = Jelentkezo::factory()->create(['email' => $user->email]);

        $torzsData = [
            'email' => $user->email,
            'vezeteknev' => 'Teszt',
            'keresztnev' => 'Elek',
            'szuletesi_nev' => 'Születési Teszt Elek',
            'anyja_neve' => 'Anyja neve',
            'szuletesi_datum' => '2000-01-01',
            'szuletesi_hely' => 'Budapest',
            'allampolgarsag' => 'Magyar',
            'lakcim' => '1111 Budapest, Teszt utca 1.',
            'taj_szam' => '123456789',
        ];

        $response = $this->actingAs($user)->postJson('/api/torzsadat-feltolt', $torzsData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('jelentkezo_torzs', [
            'jelentkezo_id' => $jelentkezo->id,
            'vezeteknev' => 'Teszt',
            'keresztnev' => 'Elek',
            'lakcim' => '1111 Budapest, Teszt utca 1.',
            'taj_szam' => '123456789'
        ]);
    }

    public function test_patch_torzsadat_frissit()
    {
        $user = User::factory()->create(['email' => 'torzs_frissit@example.com', 'role' => 0]);
        $jelentkezo = Jelentkezo::factory()->create(['email' => $user->email]);
        $torzs = JelentkezoTorzs::factory()->create([
            'jelentkezo_id' => $jelentkezo->id,
            'vezeteknev' => 'Eredeti',
            'keresztnev' => 'Név',
            'szuletesi_nev' => 'Szül Eredeti Név',
            'anyja_neve' => 'Anya neve',
            'szuletesi_datum' => '1999-12-31',
            'szuletesi_hely' => 'Pécs',
            'allampolgarsag' => 'Magyar',
            'lakcim' => '7600 Pécs, Valami út 2.',
            'taj_szam' => '987654321',
        ]);

        $updateData = [
            'vezeteknev' => 'Frissített',
            'keresztnev' => 'Vezetéknév',
            'lakcim' => '7621 Pécs, Másik utca 5.',
            'szuletesi_hely' => 'Szeged',
            'szuletesi_datum' => '1999-11-30',
            'allampolgarsag' => 'Magyar',
            'anyja_neve' => 'Új Anya',
        ];

        $response = $this->actingAs($user)->patchJson("/api/torzsadat-frissit/{$jelentkezo->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('jelentkezo_torzs', [
            'jelentkezo_id' => $torzs->jelentkezo_id,
            'vezeteknev' => 'Frissített',
            'keresztnev' => 'Vezetéknév',
            'lakcim' => '7621 Pécs, Másik utca 5.'
        ]);
    }
} 