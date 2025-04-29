<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Jelentkezo;
use Illuminate\Support\Facades\Mail;
use App\Mail\ModositasKerelemMail; 

class ModositasKerelemMailTest extends TestCase
{
    use RefreshDatabase;

    

    public function test_post_modositas_kerelem_email()
    {
        Mail::fake();

        $ugyintezo = User::factory()->create(['email' => 'modositas_kerelem@example.com', 'role' => 1]);
        $jelentkezo = Jelentkezo::factory()->create(['email' => 'applicant_requesting_mod@example.com']);

        $response = $this->actingAs($ugyintezo)->postJson('/api/modositas-kerelem-email', [
            'email' => $jelentkezo->email,
        ]);

        $response->assertStatus(200);
        
        Mail::assertSent(ModositasKerelemMail::class, function ($mail) use ($jelentkezo) {
            return $mail->hasTo($jelentkezo->email);
        });
    }
} 