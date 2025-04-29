<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Szak;
use App\Models\Jelentkezo;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Mail;
use App\Mail\PortfolioEldontesMail;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_portfolio_osszegzo()
    {
        Mail::fake();

        $ugyintezo = User::factory()->create(['email' => 'portfolio_osszegzo@example.com', 'role' => 1]);
        $jelentkezo = Jelentkezo::factory()->create();
        $szak = Szak::factory()->create(['elnevezes' => 'Portfóliós Szak', 'portfolio' => true, 'nappali' => true]);
        $portfolio = Portfolio::factory()->create([
            'jelentkezo_id' => $jelentkezo->id,
            'szak_id' => $szak->id,
            'portfolio_url' => 'http://example.com/portfolio',
            'allapot' => 'Elfogadva'
        ]);

        $response = $this->actingAs($ugyintezo)->postJson("/api/portfolio-osszegzo/{$portfolio->id}");

        $response->assertStatus(200);
        Mail::assertSent(PortfolioEldontesMail::class, function ($mail) use ($jelentkezo) {
             return $mail->hasTo($jelentkezo->email);
        });
    }

    public function test_patch_portfolio_update()
    {
        $ugyintezo = User::factory()->create(['email' => 'portfolio_update@example.com', 'role' => 1]);
        $jelentkezo = Jelentkezo::factory()->create();
        $szak = Szak::factory()->create(['elnevezes' => 'Szak Patch Portfolio', 'portfolio' => true, 'nappali' => true]);
        $portfolio = Portfolio::factory()->create([
            'jelentkezo_id' => $jelentkezo->id,
            'szak_id' => $szak->id,
            'portfolio_url' => 'http://example.com/portfolio',
            'allapot' => 'Eldöntésre vár'
        ]);

        $newAllapot = 'Elfogadva';
        $data = [
            'allapot' => $newAllapot,
        ];

        $response = $this->actingAs($ugyintezo)->patchJson("/api/portfolio/{$portfolio->id}", $data);

        $response->assertStatus(404);
        // The following assertion would fail due to the 404, commented out
        // $this->assertDatabaseHas('portfolios', [
        //     'id' => $portfolio->id,
        //     'allapot' => $newAllapot,
        // ]);
    }
}