<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Support\Facades\Validator; // to validate the email address
use App\Http\Controllers\Controller; // Add this line to import the Controller class
use App\Models\User;
use App\Models\Session;
use App\Models\EvaluationForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;

//for the smtp
use Illuminate\Support\Str;
// responsible for constructing the email that will be sent to the user
use Illuminate\Support\Facades\Mail;
use App\Mail\VerfiyEmailAddress;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    // ================= A function to check if the request has valid authorization token =================
    private function authorizeRequest(Request $request)
    {
        if (!$request->header('Authorization')) {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        $token = $request->header('Authorization');
        $jwtToken = str_replace('Bearer', '', $token);

        try {
            $user = Auth::setToken($jwtToken)->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        // Check if $user is null, indicating invalid or expired token
        if (!$user) {
            return response()->json(['Invalid token or expired'], 200);
        }

        return $user;
    }

    public function index()
    {
        return view('screen/authentication/login');
    }

    // ================= Register New User =================

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
            'email_status' => false,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => true,
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    // ================= User login =================
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            if (!($token = JWTAuth::attempt($credentials))) {
                throw ValidationException::withMessages([
                    'username' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Retrieve the authenticated user
            $user = Auth::user();

            // Define the session school year
            $sessionSchoolYear = null;
            $sessionId = null;
            // Retrieve the session school year if status is true
            $session = Session::where('session_status', true)->first();
            if ($session) {
                $sessionSchoolYear = $session->school_year;
                $sessionId = $session->id;
            }

            $evaluatedStatus = null;

            $evaluation = EvaluationForm::where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('approve_status', 'Pending')->orWhere('approve_status', 'Approved');
                })
                ->first();

            if ($evaluation && $user->role === 'Teacher' && $user->last_evaluated === $sessionSchoolYear) {
                $evaluatedStatus = 'completed';
            } elseif ($user->role === 'Student') {
                $evaluatedStatus = null;
            } else {
                $evaluatedStatus = 'not evaluated';
            }

            // Define the claims to be included in the token
            $customClaims = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'email' => $user->email,
                'session_id' => $sessionId,
                'school_year' => $sessionSchoolYear,
                'role' => $user->role,
                'teacher_evaluated' => $evaluatedStatus,
                'last_evaluated' => $user->last_evaluated,
                'exp' => now()->addDay()->timestamp, // Set expiration to 1 day from now
            ];

            // Generate the token with custom claims
            $token = JWTAuth::claims($customClaims)->attempt($credentials);

            // Return the token in the response
            return response()->json(['token' => $token], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Attempt to authenticate the user using the 'web' guard
        if (Auth::guard('web')->attempt($credentials)) {
            // Check if the user's email is verified
            if (auth()->user()->role == 'Admin') {
                // Authentication successful
                return redirect('/home')->with('success', 'Login successful!');
            } else {
                // User's role not admin
                auth()->logout(); // Log out the user
                return redirect()
                    ->back()
                    ->withErrors(['adminLogin' => 'Authorized such as Admin only']);
            }
        } else {
            // Authentication failed
            return redirect()
                ->back()
                ->withErrors(['adminLogin' => 'Invalid username or password']);
        }
    }

    // ================= Update user profile =================
    public function updateProfile(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // If user is authenticated, proceed with updating profile
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

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 201);
    }

    // ================= Update user password =================
    public function updatePassword(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

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

        return response()->json(['message' => 'Password updated successfully'], 201);
    }

    // ================= Update user email address =================
    public function updateEmail(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

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
            'email_status' => false,
        ]);

        // Send the password reset email
        Mail::to($user->email)->send(new VerfiyEmailAddress($user));

        return response()->json(['message' => 'Email updated successfully', 'user' => $user], 201);
    }
    // ================= ForgotPassword =================

    public function resetPassword(Request $request)
    {
        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // If no user found, return error response with status code 404
        if (!$user) {
            return response()->json(['error' => 'There is no account associated with that email'], 404);
        }

        // Validate the request data
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Generate a password reset token
        $token = Str::random(60);

        // Update the user's password reset token
        $user->update(['password_reset_token' => $token]);

        // Define the reset URL
        $resetUrl = 'http://127.0.0.1:8000/reset-password?token=' . $token;

        // Send the password reset email
        Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl));

        // Return success response
        return response()->json(['message' => 'Password reset email sent successfully'], 201);
    }

    // ================= Set New Password =================
    public function setNewPassword(Request $request)
    {
        // Validate the request data
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Find the user by the token
        $user = User::where('password_reset_token', $request->token)->first();

        // Check if the user exists and if the token matches
        if (!$user || $user->password_reset_token !== $request->token) {
            // Token is invalid or has already been used, return invalid_token view
            return view('screen/authentication/invalidReset');
        }

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null, // Clear the reset token
        ]);

        // Redirect the user to the client URL
        return redirect(env('CLIENT_URL'));
    }

    // ================= Retreive User base on User Role =================
    public function getUsersRole(Request $request, $role = null)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // If no role parameter is provided, return a 404 response
        if ($role === null) {
            return response()->json(['error' => 'Role parameter is required'], 404);
        }

        // If the role parameter is "all", fetch all users
        if ($role === 'all') {
            $users = User::select('id', 'first_name', 'last_name', 'role')->get();
        } else {
            // Fetch users based on the specified role
            $users = User::where('role', $role)->select('id', 'first_name', 'last_name', 'role')->get();
        }

        // Return the users as a response
        return response()->json(['users' => $users], 201);
    }

    public function getUserList(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        $users = User::where('status', true)->get();

        return response()->json(['users' => $users], 201);
    }

    public function deleteUser(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        $users = User::find($id);

        // Check if the question exists
        if (!$users) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the status of the question to false
        $users->update(['status' => false]);

        // Return success response
        return response()->json(['message' => 'User deleted successfully'], 201);
    }

    public function studentTotal(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if the authenticated user is an admin
        if ($user->role !== 'Admin') {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        // Count the total number of active students
        $totalStudents = User::where('role', 'Student')->where('status', true)->count();

        return response()->json(['message' => 'Total students', 'data' => $totalStudents], 201);
    }

    public function teacherTotal(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if the authenticated user is an admin
        if ($user->role !== 'Admin') {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        // Count the total number of active students
        $totalTeacher = User::where('role', 'Teacher')->where('status', true)->count();

        return response()->json(['message' => 'Total teachers', 'data' => $totalTeacher], 201);
    }

    // ================= Log the user out (Invalidate the token). =================

    public function logout()
    {
        Auth::logout();
        return redirect('/welcome');
    }
}
