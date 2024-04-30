<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Session;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the number of sessions to be created
        $numberOfSessions = 5; // Change this as per your requirement

        // Create sessions
            Session::create([
                'school_year' => '2024-2025', // Example school year
                'session_status' => 1, // Example session status
            ]);
        
    }
}
