<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
    {
        // Path to the JSON file
        $path1 = database_path('seeders/student_list.json');


        $json1 = File::get($path1);
        $data1 = json_decode($json1, true);

        // For Student mapping
        foreach ($data1 as $student) {
            // Convert first name and last name to have only the first letter capitalized
            $firstName = ucfirst(strtolower($student['first_name']));
            $lastName = ucfirst(strtolower($student['last_name']));

            // Generate username with lowercase names and underscores for spaces
            $username = strtolower(str_replace(' ', '_', $lastName . '@' . $firstName));

            User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => null,
                'email_status' => $student['email_status'],
                'username' => $username,
                'password' => Hash::make($username),
                'role' => $student['role'],
                'status' => $student['status'],
            ]);
        }


        // Path to the JSON file
        $path2 = database_path('seeders/teacher_list.json');

        $json2 = File::get($path2);
        $data2 = json_decode($json2, true);

        // For teacher mapping
        foreach ($data2 as $teacher) {
            // Convert first name and last name to have only the first letter capitalized
            $firstName = ucfirst(strtolower($teacher['first_name']));
            $lastName = ucfirst(strtolower($teacher['last_name']));

            // Generate username with lowercase names and underscores for spaces
            $username = strtolower(str_replace(' ', '_', $lastName . '@' . $firstName));

            // Create user
            User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => null,
                'email_status' => $teacher['email_status'],
                'username' => $username,
                'password' => Hash::make($username),
                'role' => $teacher['role'],
                'status' => $teacher['status'],
            ]);
        }
    }
}



// Principal
// Treasurer
// Registrar
// Coordinator
// Student
// Teacher
// Non-Teaching
