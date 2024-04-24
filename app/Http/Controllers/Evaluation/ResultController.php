<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Session;
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

        // Update the user's last evaluated school year
        $userEvaluation = User::where('id', $request->user_id)->update(['last_evaluated' => $request->school_year]);

        // Merge first_name and last_name into full_name
        $evaluatedFullName = $request->evaluated_full_name;

        // Create an Evaluation instance with the merged full_name
        $evaluation = EvaluationForm::create(array_merge($request->except(['evaluated_full_name']), ['evaluated_full_name' => $evaluatedFullName]));

        // Iterate over each question in the request
        foreach ($request->questions as $question) {
            // Create an EvaluationResult instance for each question
            $evaluationResult = EvaluationResult::create([
                'evaluation_id' => $evaluation->id,
                'question_id' => $question['question_id'],
                'evaluation_for' => $question['evaluation_for'],
                'question_type' => $question['question_type'],
                'question_description' => $question['question_description'],
                'rating' => $question['rating'],
                'status' => true,
            ]);
        }

        // Return a response indicating success
        return response()->json(['message' => 'Evaluation and EvaluationResult created successfully', 'evaluation' => $evaluation], 201);
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

        return response()->json(['message' => ' Evaluation result deleted successfully', 'data' => $result], 201);
    }
    // ================= Get per Rating Total =================
    public function getRatingTotal(Request $request)
    {
        // Get the type and userid from the request query parameters
        $type = $request->query('type');
        $userid = $request->query('userid');

        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if anyone has evaluated the user yet
        $evaluated = EvaluationForm::where('evaluated_id', $userid)->exists();
        if (!$evaluated) {
            return response()->json(['error' => 'No one has evaluated this user yet'], 404);
        }

        $evaluationExists = EvaluationResult::where('status', true)->exists();

        if ($evaluationExists) {
            // Fetch the total count of ratings for the specified type and user
            $ratingTotal = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')->where('evaluation_result.evaluation_for', $type)->where('evaluation.evaluated_id', $userid)->orderBy('rating')->groupBy('rating')->selectRaw('rating, count(*) as total')->pluck('total', 'rating');
        } else {
            return response()->json(['message' => 'No evaluations available'], 200);
        }

        return response()->json(['message' => 'User Evaluation found', 'data' => $ratingTotal], 201);
    }

    // ================= Get Result rating total per question =================

    public function getQuestionRating(Request $request)
    {
        // Get the type and userid from the request query parameters
        $type = $request->query('type');
        $userid = $request->query('userid');

        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if anyone has evaluated the user yet
        $evaluated = EvaluationForm::where('evaluated_id', $userid)->exists();
        if (!$evaluated) {
            return response()->json(['error' => 'No one has evaluated this user yet'], 404);
        }

        $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
            ->where('evaluation_result.evaluation_for', $type)
            ->where('evaluation.evaluated_id', $userid)
            ->orderBy('rating')
            ->groupBy('question_description', 'rating') // Include question_description in GROUP BY
            ->selectRaw('question_description, rating, count(*) as total')
            ->get();

        // Organize the results into the desired format
        $formattedResults = [];
        foreach ($questionRatings as $result) {
            $question = $result->question_description;
            $rating = $result->rating;
            $count = $result->total;

            // If the question is not yet added to the formatted results array, create an entry for it
            if (!isset($formattedResults[$question])) {
                $formattedResults[$question] = [];
            }

            // Add the rating count to the corresponding question entry
            $formattedResults[$question][$rating] = $count;
        }

        return response()->json(['message' => 'Question rating found', 'data' => $formattedResults], 201);
    }
}
