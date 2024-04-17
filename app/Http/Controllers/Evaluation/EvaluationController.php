<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EvaluationForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            return response()->json(['error' => 'Invalid token or expired'], 401);
        }

        return $user;
    }

    public function updateEvaluation(Request $request, $id)
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
        if ($user->role !== 'Admin') {
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
}
