<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Permohonan;
use App\Models\User;

class PermohonanFactory extends Factory
{
    protected $model = Permohonan::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'user_id' => $user->id,
            'username' => fake()->unique()->numerify('##########'),
            'nomor_ijazah' => fake()->unique()->numerify('##########'),
            'jumlah_lembar' => fake()->numberBetween(1, 10),
            'keperluan' => fake()->sentence(10),
            'file' => fake()->filePath(),
            'status' => fake()->numberBetween(1, 5),
            'tanggal_diambil' => fake()->optional()->dateTimeThisYear(),
        ];
    }
}