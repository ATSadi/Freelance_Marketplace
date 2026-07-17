<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@workvault.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Demo Client',
            'email' => 'client@workvault.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
        ]);

        User::factory()->create([
            'name' => 'Demo Freelancer',
            'email' => 'freelancer@workvault.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_FREELANCER,
        ]);
    }
}
