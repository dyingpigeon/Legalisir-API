<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DataAlumni;
use Hash;
use Illuminate\Database\Seeder;
use Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific role users
        User::factory()->superadmin()->create([
            'name' => 'Super Admin',
            'username' => 81234567890, // Perhatikan: integer tanpa tanda petik
            'nik' => 1234567890123456, // Perhatikan: integer tanpa tanda petik
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        // Wadir 1
        User::factory()->wadir1()->create([
            'name' => 'Wakil Direktur 1',
            'username' => 80987654321,
            'nik' => 6543210987654321,
            'email' => 'wadir1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        // Operator
        User::factory()->operator()->create([
            'name' => 'Operator Sistem',
            'username' => 81122334455,
            'nik' => 1122334455667788,
            'email' => 'operator@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        // User Biasa
        User::factory()->regular()->create([
            'name' => 'User Biasa',
            'username' => 85566778899,
            'nik' => 5566778899001122,
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        // Additional sample users
        User::factory()->regular()->create([
            'name' => 'Budi Santoso',
            'username' => 81112223334,
            'nik' => 1112223334445556,
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        User::factory()->regular()->create([
            'name' => 'Siti Rahayu',
            'username' => 82223334445,
            'nik' => 2223334445556667,
            'email' => 'siti@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        User::factory()->operator()->create([
            'name' => 'Operator 2',
            'username' => 83334445556,
            'nik' => 3334445556667778,
            'email' => 'operator2@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        // Create multiple random users using factory
        User::factory()->count(10)->create([
            'email_verified_at' => now(),
            'verification_token' => null,
            'verification_link_used_at' => now(),
        ]);

        // Create some unverified users
        User::factory()->count(3)->unverified()->create([
            'verification_token' => Str::random(60),
            'verification_link_used_at' => null,
        ]);

        // Create specific alumni data
        DataAlumni::factory()->create([
            'email' => 'the.farhanad123@gmail.com',
            'nama_Ibu' => 'bawadehel',
            'nim' => '8403095460',
            'nik' => '7152113287658775',
            'nama' => 'Farhan', // tambahkan nama karena required
            'jk' => 'laki-laki', // tambahkan jk karena required
            'agama' => 'Islam', // tambahkan agama karena required
            'tempat_lahir' => 'Jakarta', // tambahkan tempat_lahir karena required
            'tanggal_lahir' => '1990-01-01', // tambahkan tanggal_lahir karena required
            'alamat' => 'Jl. Contoh Alamat', // tambahkan alamat karena required
            'hp' => '081234567890', // tambahkan hp karena required
            'nomor_Ijazah_Elektronik' => 'IJZ-8403095460', // tambahkan nomor ijazah karena required
            'is_active' => true, // tambahkan is_active karena required
        ]);

        // Other factories...
        \App\Models\Operator::factory(5)->create();
        \App\Models\DataAlumni::factory(100)->create();
        \App\Models\Ijazah::factory(80)->create();
        \App\Models\Permohonan::factory(50)->create();
        \App\Models\RiwayatStatus::factory(100)->create();
    }
}