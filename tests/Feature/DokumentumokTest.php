<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Jelentkezo;
use App\Models\Dokumentumok;
use App\Models\DokumentumTipus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DokumentumokTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_dokumentumok()
    {
        $user = User::factory()->create(['email' => 'dok_get@example.com', 'role' => 0]);
        $jelentkezo = Jelentkezo::factory()->create(['email' => $user->email]);
        $tipus = DokumentumTipus::factory()->create(['elnevezes' => 'Személyazonosító igazolvány első oldala']);
        Storage::fake('private');
        $path = Storage::disk('private')->putFile("dokumentumok/{$jelentkezo->id}/{$tipus->id}", UploadedFile::fake()->create('szemelyi.jpg'));

        Dokumentumok::factory()->create([
            'jelentkezo_id' => $jelentkezo->id,
            'dokumentum_tipus_id' => $tipus->id,
            'fajlok' => json_encode([$path]),
        ]);

        $response = $this->actingAs($user)->getJson('/api/dokumentumok');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'szemelyi_elso',
        ]);
        $response->assertJsonFragment([$path]);
    }

    public function test_get_nyilatkozat_letoltes()
    {
        $user = User::factory()->create(['email' => 'nyil_letolt@example.com', 'role' => 0]);
        $year = date('Y');
        Storage::fake('private');
        $fileName = "nyilatkozat_{$year}_v1.docx";
        Storage::disk('private')->put("nyilatkozatok/{$year}/{$fileName}", 'fake content');

        $response = $this->actingAs($user)->get("/api/nyilatkozat-letoltes/{$year}");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=nyilatkozat_'.$year.'.docx');
    }

    public function test_post_nyilatkozat_feltoltes()
    {
        $ugyintezo = User::factory()->create(['email' => 'nyil_feltolt@example.com', 'role' => 1]); 
        Storage::fake('local'); 
        $file = UploadedFile::fake()->create('nyilatkozat.docx', 500);
        $year = date('Y');
        $expectedFilename = "nyilatkozatok/{$year}/nyilatkozat_{$year}_v1.docx";

        $response = $this->actingAs($ugyintezo)->postJson('/api/nyilatkozat-feltoltes', [
            'ev' => $year,
            'nyilatkozat' => $file,
        ]);

        $response->assertStatus(200);
        Storage::assertExists($expectedFilename);
    }
} 