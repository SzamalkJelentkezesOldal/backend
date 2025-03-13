<?php
// database/seeders/JelentkezoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jelentkezo;

class JelentkezoSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 60; $i++) {
            Jelentkezo::create([
                'nev' => "Jelentkezo $i",
                'email' => "jelentkezo$i@example.com",
                'tel' => '06201234567',
                'token' => bin2hex(random_bytes(16)),
            ]);
        }
    }
}