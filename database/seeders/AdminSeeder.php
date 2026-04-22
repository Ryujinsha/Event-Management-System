<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@tms.test'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create demo users for testing
        $studentRole = Role::where('slug', 'student')->first();
        $facultyRole = Role::where('slug', 'faculty')->first();
        $lecturerRole = Role::where('slug', 'lecturer')->first();

        User::firstOrCreate(
            ['email' => 'faculty@tms.test'],
            [
                'name' => 'Faculty Member',
                'email' => 'faculty@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $facultyRole->id,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'student@tms.test'],
            [
                'name' => 'Demo Student',
                'email' => 'student@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
                'student_id' => 'STD-2024-001',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'lecturer@tms.test'],
            [
                'name' => 'Demo Lecturer',
                'email' => 'lecturer@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $lecturerRole->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
