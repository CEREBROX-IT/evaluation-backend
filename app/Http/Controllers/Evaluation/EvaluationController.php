<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EvaluationForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
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
            return response()->json(['error' => 'Invalid token or expired'], 200);
        }

        return $user;
    }

    // Function to get Student who have already been Evaluated
    public function getUserEvaluated(Request $request)
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

        // Retrieve teachers who have already been evaluated
        $studentsEvaluated = User::where('role', 'Student')->whereHas('evaluationForms')->count();
        // Retrieve teachers who have already been evaluated
        $teachersEvaluated = User::where('role', 'Teacher')->whereHas('evaluationForms')->count();
        return response()->json(['message' => 'Total User Evaluated', 'students' => $studentsEvaluated, 'teacher' => $teachersEvaluated], 201);
    }

    // Function to get user that does not have evaluated
    public function getUsersNotEvaluated(Request $request, $status)
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

        // Retrieve users who have not yet do evaluation
        $usersNotEvaluated = User::where('status', $status)->whereDoesntHave('evaluationForms')->select('id', 'first_name', 'last_name')->get();

        return response()->json(['users not Evaluated yet' => $usersNotEvaluated], 200);
    }

    public function getComments(Request $request)
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

        // Retrieve all comments, suggestions, and user details for all evaluation forms
        $evaluationForms = DB::table('evaluation')->join('users', 'evaluation.user_id', '=', 'users.id')->select('evaluation.id', 'evaluation.user_id', 'evaluation.comment', 'evaluation.suggestion', 'users.first_name', 'users.last_name')->where('evaluation.approve_status', 'Pending')->where('evaluation.status', true)->get();

        return response()->json(['Comments & Suggestion' => $evaluationForms], 201);
    }

    public function updateEvaluation(Request $request, $id)
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

        $evaluation = EvaluationForm::find($id);

        if (!$evaluation) {
            return response()->json(['error' => 'Evaluation not found'], 404);
        }
        //update the question
        $evaluation->update([
            'comment' => $request->comment,
            'suggestion' => $request->suggestion,
        ]);

        return response()->json(['message' => 'Evaluation updated successfully', 'evaluation' => $evaluation], 201);
    }

    public function approveEvaluation(Request $request, $id)
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

        // If the ID parameter is "all", approve all evaluations
        if ($id === 'all') {
            $evaluations = EvaluationForm::all();

            foreach ($evaluations as $evaluation) {
                $evaluation->update(['approve_status' => 'Approved']);
            }

            return response()->json(['message' => 'All evaluations approved successfully'], 201);
        }

        // If a specific ID is provided, approve only that evaluation
        $evaluation = EvaluationForm::find($id);

        if (!$evaluation) {
            return response()->json(['error' => 'Evaluation not found'], 404);
        }

        $evaluation->update([
            'approve_status' => 'Approved',
        ]);

        return response()->json(['message' => 'Evaluation approved successfully', 'evaluation' => $evaluation], 201);
    }

    // =================== Temporary yooo! =================

    public function getEvaluation(Request $request, $id)
    {
        $evaluation = EvaluationForm::where('id', $id)->get();

        return response()->json(['data' => $evaluation], 200);
    }
}
