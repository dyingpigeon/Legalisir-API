<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Operator;

class OperatorFactory extends Factory
{
    protected $model = Operator::class;

    public function definition(): array
    {
        return [
            'nip' => fake()->unique()->numerify('##########'),
            'nik' => fake()->unique()->numerify('################'),
            'nama' => fake()->name(),
            'jk' => fake()->randomElement(['laki-laki', 'perempuan']),
        ];
    }
}