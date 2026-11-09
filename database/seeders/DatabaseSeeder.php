<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        

        User::create([
            'name' => 'BHW Maria',
            'email' => 'bhw@ugacsur.bhw',
            'password' => Hash::make('password123'),
            'role' => 'bhw',
        ]);

        User::create([
            'name' => 'Dr. Santos',
            'email' => 'doctor@ugacsur.bhw',
            'password' => Hash::make('password456'),
            'role' => 'healthcare_personnel',
        ]);

        User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'patient@ugacsur.bhw',
            'password' => Hash::make('password789'),
            'role' => 'patient',
        ]);
    }
}