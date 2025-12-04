<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RiwayatStatus;
use App\Models\Permohonan;
use App\Models\User;

class RiwayatStatusFactory extends Factory
{
    protected $model = RiwayatStatus::class;

    public function definition(): array
    {
        $permohonan = Permohonan::inRandomOrder()->first() ?? Permohonan::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'permohonan_id' => $permohonan->id,
            'user_id' => $user->id,
            'status_sebelum' => fake()->optional()->numberBetween(1, 5),
            'status_sesudah' => fake()->numberBetween(1, 5),
            'keterangan' => fake()->optional()->sentence(8),
        ];
    }
}