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

            // Redirect the user to a success page
            return Redirect::to('/')->with('success', 'Your email has been verified. Please log in.');
        } else {
            // Redirect the user to an error page (or handle it differently)
            return Redirect::to('/')->with('error', 'Invalid verification link.');
        }
    }
}
