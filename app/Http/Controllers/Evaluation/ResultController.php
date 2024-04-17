<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EvaluationResult;
use App\Models\EvaluationForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ResultController extends Controller
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
    // ================= Get Evaluation Result =================

    public function getQuestions(Request $request, $evaluation_for)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Fetch questions with status true
        $result = EvaluationResult::where('status', true && 'evaluation_for', $evaluation_for)->get();

        return response()->json(['result' => $result], 201);
    }

    // ================= Create Evaluation Result =================
    public function createEvaluationResult(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Create an Evaluation instance
        $evaluation = EvaluationForm::create($request->all());

        // Create an EvaluationResult instance
        $evaluationResult = EvaluationResult::create([
            'evaluation_id' => $evaluation->id,
            'question_id' => $request->question_id,
            'evaluation_for' => $request->evaluation_for,
            'question_type' => $request->question_type,
            'question_description' => $request->question_description,
            'rating' => $request->rating,
            'status' => true,
        ]);

        // Return a response indicating success
        return response()->json(['message' => 'Evaluation and EvaluationResult created successfully', 'evaluation' => $evaluation, 'evaluation_result' => $evaluationResult], 201);
    }

    // ================= Update Evaluation Result =================
    public function updateEvaluationRating(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        $result = EvaluationResult::find($id);

        if ($result) {
            return response()->json(['message' => ' Evaluation result not found'], 404);
        }

        $result = update(['rating' => $request->rating]);

        return response()->json(['message' => ' Evaluation result rating updated successfully', 'result' => $result], 201);
    }

    // ================= Delete Evaluation Result =================
    public function deleteEvaluationResult(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        $result = EvaluationResult::find($id);

        if ($result) {
            return response()->json(['message' => ' Evaluation result not found'], 404);
        }

        $result = update(['status' => false]);

        return response()->json(['message' => ' Evaluation result deleted successfully', 'result' => $result], 201);
    }

    // ================= Get per Rating Total =================

    public function getRatingTotal(Request $request, $type)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        $ratingTotal = EvaluationResult::where('evaluation_for', $type)->groupBy('rating')->selectRaw('rating, count(*) as total')->pluck('total', 'rating');

        return response()->json($ratingTotal, 201);
    }
}
