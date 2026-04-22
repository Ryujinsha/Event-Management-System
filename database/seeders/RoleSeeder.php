<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Student', 'slug' => 'student'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Faculty', 'slug' => 'faculty'],
            ['name' => 'Lecturer', 'slug' => 'lecturer'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
