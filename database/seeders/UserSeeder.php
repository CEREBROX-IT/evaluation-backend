<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'email_status' => 1,
            'username' => 'admin',
            'password' => Hash::make('qwerty'), // You may want to change this
            'role' => 'Admin',
            'status' => 1,
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'superadmin@example.com',
            'email_status' => 1,
            'username' => 'superadmin',
            'password' => Hash::make('superadmin'), // You may want to change this
            'role' => 'SuperAdmin',
            'status' => 1,
        ]);

        // Create a student user
        User::create([
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@example.com',
            'email_status' => 1,
            'username' => 'student',
            'password' => Hash::make('qwerty'), // You may want to change this
            'role' => 'Student',
            'status' => 1,
        ]);

        // Create a principal user
        User::create([
            'first_name' => 'Principal',
            'last_name' => 'User',
            'email' => 'principal@example.com',
            'email_status' => 1,
            'username' => 'principal',
            'password' => Hash::make('qwerty'), // You may want to change this
            'role' => 'Principal',
            'status' => 1,
        ]);

        // Create a treasurer user
        User::create([
            'first_name' => 'Treasurer',
            'last_name' => 'User',
            'email' => 'treasurer@example.com',
            'email_status' => 1,
            'username' => 'treasurer',
            'password' => Hash::make('qwerty'), // You may want to change this
            'role' => 'Treasurer',
            'status' => 1,
        ]);

        // Create a registrar user
        User::create([
            'first_name' => 'Registrar',
            'last_name' => 'User',
            'email' => 'registrar@example.com',
            'email_status' => 1,
            'username' => 'registrar',
            'password' => Hash::make('qwerty'), // You may want to change this
            'role' => 'Registrar',
            'status' => 1,
        ]);

        // Create a coordinator user
        User::create([
            'first_name' => 'Coordinator',
            'last_name' => 'User',
            'email' => 'coordinator@example.com',
            'email_status' => 1,
            'username' => 'coordinator',
            'password' => Hash::make('qwerty'), // You may want to change this
            'role' => 'Coordinator',
            'status' => 1,
        ]);

        // Create a teacher user
        User::create([
            'first_name' => 'Teacher',
            'last_name' => 'User',
            'email' => 'teacher@example.com',
            'email_status' => 1,
            'username' => 'teacher',
            'password' => Hash::make('qwerty'), // You may want to change this
            'role' => 'Teacher',
            'status' => 1,
        ]);
    }
}

// Princial
// Treasurer
// Registrar
// Coordinator
// Student
// Teacher
// Admin
// SuperAdmin
