<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jelentkezo>
 */
class JelentkezoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nev'   => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'tel'   => $this->faker->phoneNumber,
            'token' => Str::random(32),
        ];
    }
}
