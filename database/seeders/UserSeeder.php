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
            'password' => "qwerty", 
            'role' => 'admin',
            'status' => 1,
        ]);

        // Create a teacher user
        User::create([
            'first_name' => 'Teacher',
            'last_name' => 'User',
            'email' => 'teacher@example.com',
            'email_status' => 1,
            'username' => 'teacher',
            'password' => "qwerty", 
            'role' => 'teacher',
            'status' => 1,
        ]);

        // Create a student user
        User::create([
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@example.com',
            'email_status' => 1,
            'username' => 'student',
            'password' => "qwerty", 
            'role' => 'student',
            'status' => 1,
        ]);
    }
}
