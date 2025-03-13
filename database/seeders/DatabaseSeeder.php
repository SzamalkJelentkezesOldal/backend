<?php

namespace Database\Seeders;

use App\Models\Jelentkezo;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        //Jelentkezo::factory()->count(100)->create();
        //User::factory()->count(10)->create();
        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
        $this->call([
            JelentkezoSeeder::class,
            JelentkezesSeeder::class,
        ]);
    }
}
