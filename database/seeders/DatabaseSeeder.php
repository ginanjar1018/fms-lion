<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrator
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // Viewer
        User::create([
            'name' => 'Viewer',
            'email' => 'viewer@example.com',
            'password' => 'password',
            'role' => 'viewer',
        ]);

        // Departments
        Department::create([
            'name' => 'IT',
        ]);

        Department::create([
            'name' => 'Finance',
        ]);

        Department::create([
            'name' => 'Human Resources',
        ]);
    }
}