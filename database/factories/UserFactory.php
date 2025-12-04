<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = fake()->name();
        
        return [
            'name' => $nama,
            'username' => fake()->unique()->numerify('08##########'), // Format phone number sebagai integer
            'nik' => fake()->unique()->numerify('################'), // 16 digit NIK
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['user']),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user has operator role.
     */
    public function operator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'operator',
        ]);
    }

    /**
     * Indicate that the user has wadir1 role.
     */
    public function wadir1(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'wadir1',
        ]);
    }

    /**
     * Indicate that the user has superadmin role.
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
        ]);
    }

    /**
     * Indicate that the user has regular user role.
     */
    public function regular(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }
}