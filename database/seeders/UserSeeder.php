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
            'first_name' => 'Special Admin',
            'last_name' => 'Special',
            'email' => 'specialAdmin@example.com',
            'email_status' => 1,
            'username' => 'specialAdmin',
            'password' => Hash::make('qwerty'),
            'role' => 'SpecialAdmin',
            'status' => 1,
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'superadmin@example.com',
            'email_status' => 1,
            'username' => 'superadmin',
            'password' => Hash::make('superadmin'),
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
            'password' => Hash::make('qwerty'),
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
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'first_name' => 'Teacher' . $i,
                'last_name' => 'User' . $i,
                'email' => 'teacher' . $i . '@example.com', // Using $i to make each email unique
                'email_status' => 1,
                'username' => 'teacher' . $i,
                'password' => Hash::make('qwerty'), // You may want to change this
                'role' => 'Teacher',
                'status' => 1,
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'first_name' => 'None Teaching' . $i,
                'last_name' => 'User' . $i,
                'email' => 'NonTeaching' . $i . '@example.com', // Using $i to make each email unique
                'email_status' => 1,
                'username' => 'nonteaching' . $i,
                'password' => Hash::make('qwerty'), // You may want to change this
                'role' => 'Non-Teaching',
                'status' => 1,
            ]);
        }
    }
}

// Princial
// Treasurer
// Registrar
// Coordinator
// Student
// Teacher
// Non-Teaching
