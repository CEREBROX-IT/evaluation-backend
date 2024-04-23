<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\User;
use App\Models\EvaluationForm;
use App\Models\EvaluationResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    // ================= A function to check if the request has valid authorization token =================
    private function authorizeRequest(Request $request)
    {
        if (!$request->header('Authorization')) {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        $token = $request->header('Authorization');
        $jwtToken = str_replace('Bearer ', '', $token);

        try {
            $user = Auth::setToken($jwtToken)->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        // Check if $user is null, indicating invalid or expired token
        if (!$user) {
            return response()->json(['error' => 'Invalid token or expired'], 401);
        }

        return $user;
    }

    public function createSessionSchoolYear(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if the authenticated user is an admin
        if ($user->role !== 'Admin' && $user->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Update the status of the previous session to false
            Session::where('session_status', true)->update(['session_status' => false]);

            // Get the ID of the previous session
            $previousSessionId = Session::where('session_status', false)->max('id');

            // If there is a previous session, update its associated records
            if ($previousSessionId) {
                // Update the status of EvaluationForm records for the previous session
                EvaluationForm::where('session_id', $previousSessionId)->update(['status' => false]);

                // Get the IDs of EvaluationForm records for the previous session
                $evaluationFormIds = EvaluationForm::where('session_id', $previousSessionId)->pluck('id');

                // Update the status of EvaluationResult records related to these EvaluationForm IDs
                EvaluationResult::whereIn('evaluation_id', $evaluationFormIds)->update(['status' => false]);
            }

            // Create a new session
            $session = Session::create([
                'school_year' => $request->school_year,
                'session_status' => true,
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'School year Session created successfully', 'session' => $session], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if an exception occurs
            DB::rollback();

            // Return an error response
            return response()->json(['error' => 'Failed to create session'], 500);
        }
    }

    public function getSessionList(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if the authenticated user is an admin or super admin
        if ($user->role !== 'Admin' && $user->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        // Retrieve all sessions from the Session model
        $sessionList = Session::all();

        return response()->json(['message' => 'List of Sessions', 'data' => $sessionList], 200);
    }
}
