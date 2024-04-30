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
        // Create a teacher user
    //    for ($i = 1; $i <= 3; $i++) {
    //         User::create([
    //             'first_name' => 'Teacher' . $i,
    //             'last_name' => 'User',
    //             'email' => 'teacher' . $i . '@example.com',
    //             'email_status' => 1,
    //             'username' => 'teacher' . $i,
    //             'password' => Hash::make('qwerty'), 
    //             'role' => 'Teacher',
    //             'status' => 1,
    //         ]);
    //     }

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
    }
}
