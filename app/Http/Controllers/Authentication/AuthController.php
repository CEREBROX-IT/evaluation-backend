<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Support\Facades\Validator; // to validate the email address
use App\Http\Controllers\Controller; // Add this line to import the Controller class
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;

//for the smtp
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    // Register New User

    public function register(Request $request)
    {
        // Check if the username already exists
        $existingUser = User::where('username', $request->username)->first();

        if ($existingUser) {
            return response()->json(['message' => 'Username already exists'], 202);
        }

        // If email is empty string or not provided, set it to null
        $email = $request->filled('email') ? $request->email : null;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $email,
            'email_status' => 0,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    /**
     * Authenticate a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!($token = JWTAuth::attempt($credentials))) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Define the claims to be included in the token
        $customClaims = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'exp' => now()->addDay()->timestamp, // Set expiration to 1 day from now
        ];

        // Generate the token with custom claims
        $token = JWTAuth::claims($customClaims)->attempt($credentials);

        // Return the token in the response
        return response()->json(['token' => $token]);
    }

    public function updateProfile(Request $request, $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update user's profile information
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    // Update user password
    public function updatePassword(Request $request, $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    // Update user email address
    public function updateEmail(Request $request, $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // Update user's email
        $user->update([
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Email updated successfully', 'user' => $user]);
    }

    // Log the user out (Invalidate the token).

    public function logout(Request $request)
    {
        JWTAuth::parseToken()->invalidate(); // Invalidate the JWT token

        return response()->json(['message' => 'Successfully logged out']);
    }
}
