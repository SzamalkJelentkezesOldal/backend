<?php

namespace Database\Seeders;

use App\Models\Jelentkezo;
use App\Models\JelentkezoTorzs;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JelentkezoTorzsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jelentkezok = Jelentkezo::all();
        $jelentkezokToUsers = $jelentkezok->random(round($jelentkezok->count() * 0.7)); // Véletlenszerűen kiválasztjuk a jelentkezők 70%-át

        foreach ($jelentkezok as $jelentkezo) {
            // Ellenőrizzük, hogy létezik-e már a rekord
            if (JelentkezoTorzs::where('jelentkezo_id', $jelentkezo->id)->exists()) {
                continue; // Ha létezik, kihagyjuk
            }

            $nevek = explode(' ', $jelentkezo->nev); // Szétválasztjuk vezetéknévre és keresztnévre
            $vezeteknev = $nevek[0];
            $keresztnev = $nevek[1] ?? '';

            // Létrehozzuk a jelentkezo_torzs rekordot
            JelentkezoTorzs::create([
                'jelentkezo_id' => $jelentkezo->id,
                'vezeteknev' => $vezeteknev,
                'keresztnev' => $keresztnev,
                'adoazonosito' => str_pad($jelentkezo->id, 10, '0', STR_PAD_LEFT), // Egyedi azonosító
                'lakcim' => "Budapest, Utca {$jelentkezo->id}.",
                'taj_szam' => str_pad($jelentkezo->id, 9, '0', STR_PAD_LEFT), // Egyedi TAJ szám
                'szuletesi_hely' => 'Debrecen',
                'szuletesi_nev' => $jelentkezo->nev,
                'szuletesi_datum' => now()->subYears(rand(18, 50))->format('Y-m-d'),
                'allampolgarsag' => 'magyar',
                'anyja_neve' => 'AnyjaNeve' . $jelentkezo->id,
            ]);

            // Ha a jelentkező a kiválasztott 70%-ban van, létrehozzuk a users táblában is
            if ($jelentkezokToUsers->contains($jelentkezo)) {
                User::create([
                    'name' => $jelentkezo->nev,
                    'email' => $jelentkezo->email,
                    'password' => bcrypt('password'), // Alapértelmezett jelszó
                ]);
            }
        }
    }
}
