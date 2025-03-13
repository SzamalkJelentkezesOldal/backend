<?php
// database/seeders/JelentkezesSeeder.php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jelentkezes;
use App\Models\Szak;

class JelentkezesSeeder extends Seeder
{
    public function run()
    {
        $szakIds = Szak::pluck('id')->toArray();
        $allapotId = 2; 

        for ($i = 1; $i <= 60; $i++) {
            // Ketto szakra jelentkezik egy jelentkező
            $numSzak = rand(2, count($szakIds));
            $selectedSzakIds = array_rand(array_flip($szakIds), $numSzak);

            foreach ($selectedSzakIds as $szakId) {
                // megnézzük, hogy a jelentkező már jelentkezett-e erre a szakra
                if (!Jelentkezes::where('jelentkezo_id', $i)->where('szak_id', $szakId)->exists()) {
                    Jelentkezes::create([
                        'jelentkezo_id' => $i,
                        'szak_id' => $szakId,
                        'allapot' => $allapotId,
                    ]);
                }
            }

            // Legalább egy jelentkező esti tagozatra is jelentkezik
            if ($i % 10 == 0) {
                $extraSzakId = $szakIds[array_rand($szakIds)];
                if (!Jelentkezes::where('jelentkezo_id', $i)->where('szak_id', $extraSzakId)->exists()) {
                    Jelentkezes::create([
                        'jelentkezo_id' => $i,
                        'szak_id' => $extraSzakId,
                        'allapot' => $allapotId,
                    ]);
                }
            }
        }
    }
}