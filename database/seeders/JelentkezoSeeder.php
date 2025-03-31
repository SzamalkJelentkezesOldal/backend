<?php
// database/seeders/JelentkezoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jelentkezo;

class JelentkezoSeeder extends Seeder
{
    public function run()
    {
        
        $nevek = [
            'Kovács János', 'Nagy Péter', 'Szabó Anna', 'Tóth László', 'Kiss Éva',
            'Molnár Zoltán', 'Varga Katalin', 'Balogh István', 'Németh Mária', 'Farkas Gábor',
            'Papp Judit', 'Horváth Tamás', 'Takács Ádám', 'Juhász Eszter', 'Oláh Attila',
            'Simon Gabriella', 'Fekete András', 'Lakatos Zsuzsanna', 'Bíró Krisztián', 'Szalai Edit',
            'Hegedűs Lilla', 'Kocsis Márk', 'Sipos Dorina', 'Török Dávid', 'Pintér Viktória',
            'Szűcs Roland', 'Gál Noémi', 'Kelemen Balázs', 'Bognár Dóra', 'Hajdu Levente',
            'Fülöp Alexandra', 'Bálint Tamara', 'Király Ákos', 'Vincze Nóra', 'Boros Gergely',
            'Major László', 'Veres Zoltán', 'Soós Anikó', 'Hegyi Csaba', 'Kozma Patrik',
            'Pálfi Adrienn', 'Kiss Tamás', 'Tóth Zsófia', 'Nagy Ádám', 'Szabó Lili',
            'Molnár Bence', 'Varga Réka', 'Balogh Márton', 'Németh Laura', 'Farkas Áron',
            'Papp Vivien', 'Horváth Levente', 'Takács Nóra', 'Juhász Gergő', 'Oláh Petra',
            'Simon Dániel', 'Fekete Hanna', 'Lakatos Máté', 'Bíró Boglárka', 'Szalai Ágnes'
        ];

        foreach ($nevek as $index => $nev) {
            $email = strtolower(str_replace(' ', '.', $nev)) . '@example.com';
            Jelentkezo::create([
                'nev' => $nev,
                'email' => $email,
                'tel' => '06201234567',
                'token' => bin2hex(random_bytes(16)),
            ]);
        }
    
    }
}