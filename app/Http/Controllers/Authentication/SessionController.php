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
        if (!$user) {
            return response()->json(['error' => 'Invalid token or expired'], 401);
        }
        return $user;
    }

    public function createSessionSchoolYear(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        DB::beginTransaction();

        try {
            // Update the status of the previous session to false
            Session::where('session_status', true)->update(['session_status' => false]);
            // Get the ID of the previous session
            $previousSessionId = Session::where('session_status', false)->max('id');
            // If there is a previous session, update its associated records
            if ($previousSessionId) {
                EvaluationForm::where('session_id', $previousSessionId)->update(['status' => false]);
                $evaluationFormIds = EvaluationForm::where('session_id', $previousSessionId)->pluck('id');
                EvaluationResult::whereIn('evaluation_id', $evaluationFormIds)->update(['status' => false]);
            }
            // Create a new session
            $session = Session::create([
                'school_year' => $request->school_year,
                'session_status' => true,
            ]);

            DB::commit();
            return response()->json(['message' => 'School year Session created successfully', 'session' => $session], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create session'], 500);
        }
    }

    public function getSessionList(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        $sessionList = Session::all();
        return response()->json(['message' => 'List of Sessions', 'data' => $sessionList], 200);
    }
}
