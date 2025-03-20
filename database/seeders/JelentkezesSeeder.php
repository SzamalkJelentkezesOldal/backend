<?php
// database/seeders/JelentkezesSeeder.php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jelentkezes;
use App\Models\Szak;
use Illuminate\Support\Carbon;

class JelentkezesSeeder extends Seeder
{

    public function run()
    {
        $szakIds = Szak::pluck('id')->toArray();
        $allapotId = 2; // Assuming 2 is a valid allapot ID

        for ($i = 1; $i <= 60; $i++) {
            // Ensure each jelentkezo applies to at least 2 szak
            $numSzak = rand(2, count($szakIds));
            $selectedSzakIds = array_rand(array_flip($szakIds), $numSzak);

            foreach ($selectedSzakIds as $szakId) {
                // Generate a random date between the start of the year and now
                $randomDate = Carbon::createFromTimestamp(rand(strtotime('first day of January this year'), time()));

                // Check if the jelentkezo already applied to this szak
                if (!Jelentkezes::where('jelentkezo_id', $i)->where('szak_id', $szakId)->exists()) {
                    Jelentkezes::create([
                        'jelentkezo_id' => $i,
                        'szak_id' => $szakId,
                        'allapot' => $allapotId,
                        'created_at' => $randomDate,
                        'updated_at' => $randomDate,
                    ]);
                }
            }

            // Ensure at least one jelentkezo applies to both day and evening courses
            if ($i % 10 == 0) {
                $extraSzakId = $szakIds[array_rand($szakIds)];
                $randomDate = Carbon::createFromTimestamp(rand(strtotime('first day of January this year'), time()));
                if (!Jelentkezes::where('jelentkezo_id', $i)->where('szak_id', $extraSzakId)->exists()) {
                    Jelentkezes::create([
                        'jelentkezo_id' => $i,
                        'szak_id' => $extraSzakId,
                        'allapot' => $allapotId,
                        'created_at' => $randomDate,
                        'updated_at' => $randomDate,
                    ]);
                }
            }
        }
    }
}