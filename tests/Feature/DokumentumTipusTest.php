<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\DokumentumTipus;

class DokumentumTipusTest extends TestCase
{
    use RefreshDatabase;

    // Tests for DokumentumTipusController endpoints will be added here

    public function test_get_dokumentum_tipusok()
    {
        DokumentumTipus::factory()->count(3)->create(['elnevezes' => 'Teszt Tipus']);

        $response = $this->getJson('/api/dokumentum-tipusok');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'elnevezes',
            ]
        ]);
    }
} 