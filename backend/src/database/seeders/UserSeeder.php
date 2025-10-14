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
        User::create([
            'name' => 'Siswa1',
            'email' => 'siswa1@example.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Admin1',
            'email' => 'admin1@example.com',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Guru1',
            'email' => 'guru1@example.com',
            'role' => 'teacher',
            'password' => Hash::make('password123'),
        ]);
    }
}
