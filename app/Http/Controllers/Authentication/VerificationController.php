<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller
{
    public function verify($id, $hash)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists and the hash matches
        if ($user && sha1($user->email) === $hash) {
            // Update the user's verified status
            $user->email_status = true;
            $user->save();

            // Return the view with a success message
            return view('layouts.verifiedComplete')->with('success', 'Your email has been verified. Please log in.');
        } else {
            // Return the view with an error message
            return view('screen/authentication/login')->with('error', 'Invalid verification link.');
        }
    }
}
