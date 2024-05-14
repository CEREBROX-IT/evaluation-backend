<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EvaluationForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    // Function to get Users who have already been Evaluated
    public function getUserEvaluated(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Retrieve the total count of users who have already been evaluated
        $adminsEvaluated = User::whereIn('role', ['Principal', 'Treasurer', 'Registrar', 'Coordinator'])
            ->whereHas('evaluationForms')
            ->count();
        $studentsEvaluated = User::where('role', 'Student')->whereHas('evaluationForms')->count();
        $teachersEvaluated = User::where('role', 'Teacher')->whereHas('evaluationForms')->count();
        $nonTeachingEvaluated = User::where('role', 'Non-Teaching')->whereHas('evaluationForms')->count();

        // Combine the count of admin roles into a single category called "Admins"
        $totalAdmins = $adminsEvaluated;

        // Return the response
        return response()->json(['message' => 'Total Users Evaluated', 'admins' => $totalAdmins, 'students' => $studentsEvaluated, 'teachers' => $teachersEvaluated, 'non_teaching' => $nonTeachingEvaluated], 201);
    }

    // Function to get user that does not have evaluated
    public function getUsersNotEvaluated(Request $request, $status)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Retrieve users who have not yet do evaluation
        $usersNotEvaluated = User::where('status', $status)->whereDoesntHave('evaluationForms')->select('id', 'first_name', 'last_name', 'role')->get();

        return response()->json(['users not Evaluated yet' => $usersNotEvaluated], 200);
    }

    public function getComments(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        $evaluationForms = DB::table('evaluation')->join('users', 'evaluation.evaluated_id', '=', 'users.id')->select('evaluation.id', 'evaluation.evaluated_id', 'evaluation.comment', 'evaluation.suggestion', 'users.role AS evaluator_role', DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS evaluator_full_name"), 'evaluation.evaluated_full_name', 'evaluation.approve_status', 'evaluation.updated_at')->where('evaluation.approve_status', 'Pending')->where('evaluation.status', true)->get();

        return response()->json(['Comments & Suggestion' => $evaluationForms], 201);
    }

    public function recentApproveComment(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Retrieve all comments, suggestions, and user details for all evaluation forms
        $evaluationForms = DB::table('evaluation')
            ->join('users', 'evaluation.evaluated_id', '=', 'users.id')
            ->select('evaluation.id', 'evaluation.evaluated_id', 'evaluation.comment', 'evaluation.suggestion', 'users.role AS evaluator_role', DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS evaluator_full_name"), 'evaluation.evaluated_full_name', 'evaluation.approve_status', 'evaluation.updated_at')
            ->whereIn('evaluation.approve_status', ['Approved'])
            ->where('evaluation.status', true)
            ->get();

        $evaluationForms = $evaluationForms->filter(function ($form) {
            if ($form->approve_status === 'Approved') {
                $approvalDate = Carbon::parse($form->updated_at);
                $now = Carbon::now();
                return $approvalDate->diffInDays($now) <= 24 * 60 * 60;
            }
            return true;
        });

        return response()->json(['Recent Approve Comments & Suggestion' => $evaluationForms], 201);
    }

    public function officeServiceComments(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Retrieve comments, suggestions, and user details for approved evaluation forms
        $evaluationForms = DB::table('evaluation')->join('users', 'evaluation.user_id', '=', 'users.id')->select('evaluation.id', 'evaluation.user_id', 'users.first_name', 'users.last_name', 'evaluation.comment', 'evaluation.suggestion', 'evaluation.approve_status', 'evaluation.updated_at')->where('evaluation.status', true)->where('evaluation.approve_status', 'Approved')->where('evaluation.office_services', '!=', 'N/a')->get();

        return response()->json(['message' => 'Office Service Comments & Suggestions', 'data' => $evaluationForms], 201);
    }

    public function updateEvaluation(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
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
