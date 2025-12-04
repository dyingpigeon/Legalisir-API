<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataAlumni;

class DataAlumniFactory extends Factory
{
    protected $model = DataAlumni::class;

    public function definition(): array
    {
        return [
            'nim' => fake()->unique()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'nik' => fake()->unique()->numerify('################'),
            'nama' => fake()->name(),
            'jk' => fake()->randomElement(['laki-laki', 'perempuan']),
            'nama_Ibu' => fake()->name('female'),
            'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d'),
            'alamat' => fake()->address(),
            'hp' => fake()->numerify('08##########'),
            'nomor_Ijazah_Elektronik' => fake()->unique()->bothify('IJZ-##########'),
            'is_active' => fake()->boolean(0), // 80% chance true
        ];
    }
}