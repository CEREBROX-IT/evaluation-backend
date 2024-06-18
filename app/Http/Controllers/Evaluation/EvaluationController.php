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
        if (!$user) {
            return response()->json(['error' => 'Invalid token or expired'], 200);
        }
        return $user;
    }

    // ================= Function to get Users who have already been Evaluated =================
    public function getUserEvaluated(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
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
        return response()->json(['message' => 'Total Users Evaluated', 'admins' => $totalAdmins, 'students' => $studentsEvaluated,
        'teachers' => $teachersEvaluated, 'non_teaching' => $nonTeachingEvaluated], 201);
    }

    // ================= Function to get user that does not have evaluated =================
    public function getUsersNotEvaluated(Request $request, $status)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        $usersNotEvaluated = User::where('status', $status)->whereDoesntHave('evaluationForms')->select('id', 'first_name', 'last_name', 'role')->get();
        return response()->json(['users not Evaluated yet' => $usersNotEvaluated], 200);
    }


    // ================= Function to retrieve Pending Comments =================
    public function getComments(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        // Retrieve all comments, suggestions, and user details for all evaluation forms
        $evaluationForms = EvaluationForm::join('users as evaluated', 'evaluation.evaluated_id', '=', 'evaluated.id')->join('users as evaluator',
        'evaluation.user_id', '=', 'evaluator.id')->select('evaluation.id', 'evaluation.comment', 'evaluation.suggestion', 'evaluation.user_id AS evaluator_id',
        DB::raw("CONCAT(evaluator.first_name, ' ', evaluator.last_name) AS evaluator_full_name"), 'evaluator.role AS evaluator_role', 'evaluation.evaluated_id',
        'evaluation.evaluated_full_name', 'evaluated.role AS evaluated_role', 'evaluation.approve_status', 'evaluation.updated_at')->where('evaluation.approve_status', 'Pending')
        ->where('evaluation.status', true)->get();

        return response()->json(['Comments & Suggestion' => $evaluationForms], 201);
    }

    // ================= Function to retrieve recently Approved Comments =================
    public function recentApproveComment(Request $request)
    {

        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        // Retrieve all comments, suggestions, and user details for all evaluation forms
        $evaluationForms = EvaluationForm::join('users as evaluated', 'evaluation.evaluated_id', '=', 'evaluated.id')
            ->join('users as evaluator', 'evaluation.user_id', '=', 'evaluator.id')->select('evaluation.id', 'evaluation.comment',
            'evaluation.suggestion', 'evaluation.user_id AS evaluator_id', DB::raw("CONCAT(evaluator.first_name, ' ', evaluator.last_name) AS evaluator_full_name"),
            'evaluator.role AS evaluator_role', 'evaluation.evaluated_id', 'evaluation.evaluated_full_name', 'evaluated.role AS evaluated_role', 'evaluation.approve_status',
            'evaluation.updated_at')
            ->whereIn('evaluation.approve_status', ['Approved'])
            ->where('evaluation.status', true)
            ->get();

        $evaluationForms = $evaluationForms->filter(function ($form) {
            if ($form->approve_status === 'Approved') {
                $approvalDate = Carbon::parse($form->updated_at);
                $now = Carbon::now();
                return $approvalDate->diffInDays($now) <= 10;
            }
            return true;
        });

        return response()->json(['Recent Approve Comments & Suggestion' => $evaluationForms], 201);
    }

    public function officeServiceComments(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        // Retrieve comments, suggestions, and user details for approved evaluation forms
        $evaluationForms = EvaluationForm::join('users', 'evaluation.user_id', '=', 'users.id')->select('evaluation.id', 'evaluation.user_id',
        'users.first_name', 'users.last_name', 'evaluation.comment', 'evaluation.suggestion', 'evaluation.approve_status', 'evaluation.updated_at')
        ->where('evaluation.status', true)->where('evaluation.approve_status', 'Approved')
        ->where('evaluation.office_services', '!=', 'N/a')->get();

        return response()->json(['message' => 'Office Service Comments & Suggestions', 'data' => $evaluationForms], 201);
    }

    public function AdministratorEvalComments(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        // Retrieve comments, suggestions, and user details for approved evaluation forms
        $evaluationForms = EvaluationForm::join('users', 'evaluation.user_id', '=', 'users.id')->select('evaluation.id', 'evaluation.user_id',
        'users.first_name', 'users.last_name', 'evaluation.comment', 'evaluation.suggestion', 'evaluation.approve_status', 'evaluation.updated_at')
        ->where('evaluation.status', true)->where('evaluation.approve_status', 'Approved')
        ->where('evaluation.length_of_service', '!=', 'N/a')->get();

        return response()->json(['message' => 'Administrator Evaluation Comments & Suggestions', 'data' => $evaluationForms], 201);
    }

    // ================= Function to update Comments =================
    public function updateEvaluation(Request $request, $id)
    {

        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
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

        // ================= Function to approve Comments =================
    public function approveEvaluation(Request $request, $id)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        if ($id === 'all') {
            $evaluations = EvaluationForm::all();

            foreach ($evaluations as $evaluation) {
                $evaluation->update(['approve_status' => 'Approved']);
            }
            return response()->json(['message' => 'All evaluations approved successfully'], 201);
        }
        $evaluation = EvaluationForm::find($id);

        if (!$evaluation) {
            return response()->json(['error' => 'Evaluation not found'], 404);
        }
        $evaluation->update([
            'approve_status' => 'Approved',
        ]);
        return response()->json(['message' => 'Evaluation approved successfully', 'evaluation' => $evaluation], 201);
    }
}
