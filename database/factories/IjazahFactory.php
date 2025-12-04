<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ijazah;

class IjazahFactory extends Factory
{
    protected $model = Ijazah::class;

    public function definition(): array
    {
        return [
            'nomor_ijazah' => fake()->unique()->numerify('##########'),
            'nim' => fake()->unique()->numerify('##########'),
            'path_file' => fake()->filePath(),
        ];
    }
}