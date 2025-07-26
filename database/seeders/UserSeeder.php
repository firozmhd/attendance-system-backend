<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a teacher user
        User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'), // Use a strong password in production!
            'role' => 'teacher',
        ]);

        // Create some student users
        User::create([
            'name' => 'Student A',
            'email' => 'studentA@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student B',
            'email' => 'studentB@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student C',
            'email' => 'studentC@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
    }
}