<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\ClassRoom;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk user, guru, siswa, admin.
     */
    public function run(): void
    {
        // ===== Buat Jurusan =====
        $rpl = Department::firstOrCreate(
            ['code' => 'RPL'],
            ['name' => 'Rekayasa Perangkat Lunak']
        );

        $mm = Department::firstOrCreate(
            ['code' => 'MM'],
            ['name' => 'Multimedia']
        );

        // ===== Buat Kelas =====
        $kelasRPL1 = ClassRoom::firstOrCreate(
            ['name' => 'X-RPL 1', 'department_id' => $rpl->id]
        );

        $kelasMM1 = ClassRoom::firstOrCreate(
            ['name' => 'X-MM 1', 'department_id' => $mm->id]
        );

        // ===== Buat Admin =====
        User::firstOrCreate(
            ['email' => 'admin1@example.com'],
            [
                'name' => 'Admin 1',
                'username' => 'admin1',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // ===== Buat Guru =====
        User::firstOrCreate(
            ['email' => 'ahmad.sobana@example.com'],
            [
                'name' => 'Ahmad Sobana',
                'username' => 'ahmadsobana',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
            ]
        );

        User::firstOrCreate(
            ['email' => 'sri.rahayu@example.com'],
            [
                'name' => 'Sri Rahayu',
                'username' => 'srirahayu',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
            ]
        );

        // ===== Buat Siswa =====
        User::firstOrCreate(
            ['email' => 'andi.rahman@example.com'],
            [
                'name' => 'Andi Rahman',
                'username' => 'andirahman',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'class_id' => $kelasRPL1->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'rahmawati@example.com'],
            [
                'name' => 'Rahmawati',
                'username' => 'rahmawati',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'class_id' => $kelasMM1->id,
            ]
        );
    }
}
