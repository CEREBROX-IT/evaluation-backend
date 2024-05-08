<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Session;
use App\Models\EvaluationResult;
use App\Models\EvaluationForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    // public function getQuestions(Request $request, $type)
    // {
    //     // Check if the request has valid authorization token
    //     $user = $this->authorizeRequest($request);
    //     if (!$user instanceof User) {
    //         return $user; // Return the response if authorization fails
    //     }

    //     // Fetch questions with status true
    //     $result = EvaluationResult::where('status', true && 'type', $type)->get();

    //     return response()->json(['result' => $result], 201);
    // }

    public function getApproveComments(Request $request, $userid)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }
        $evaluationApprove = DB::table('evaluation')
            ->join('users', 'evaluation.evaluated_id', '=', 'users.id')
            ->select('evaluation.id', 'evaluation.comment', 'evaluation.suggestion')
            ->where('users.id', $userid)
            ->where('evaluation.status', true)
            ->where('evaluation.approve_status', 'Approved')
            ->orderBy('evaluation.id', 'desc') // Sort by evaluation.id in descending order
            ->get();

        return response()->json(['Comments & Suggestion Approved' => $evaluationApprove], 201);
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
                'type' => $question['type'],
                'question_group' => $question['question_group'],
                'evaluation_type' => $question['evaluation_type'],
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

    public function getRatingTotal(Request $request)
    {
        // Get the type and userid from the request query parameters
        // $type = $request->query('type');
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
            $ratingTotal = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')->where('evaluation.evaluated_id', $userid)->orderBy('rating')->groupBy('rating')->selectRaw('rating, count(*) as total')->pluck('total', 'rating');
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

        $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')->where('evaluation_result.type', $type)->where('evaluation.evaluated_id', $userid)->where('evaluation_result.status', true)->groupBy('question_id', 'question_description')->select('question_id', 'question_description')->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')->get();

        // Organize the results into the desired format
        $formattedResults = [];
        foreach ($questionRatings as $result) {
            $formattedResult = [
                'id' => $result->question_id,
                'question_description' => $result->question_description,
            ];

            // Add ratings and their counts to the formatted result
            for ($i = 1; $i <= 5; $i++) {
                if ($result->{$i}) {
                    $formattedResult[$i] = $result->{$i};
                }
            }

            // Add the formatted result to the final array
            $formattedResults[] = $formattedResult;
        }

        return response()->json(['data' => $formattedResults], 200);
    }

    public function checkEvaluation(Request $request, $userId)
    {
        // Retrieve the evaluations related to the provided user ID
        $evaluations = EvaluationForm::where('user_id', $userId)->get();

        // Retrieve all the unique types of questions that the user has already evaluated
        $evaluatedTypes = EvaluationResult::whereIn('evaluation_id', $evaluations->pluck('id'))->pluck('type')->unique();

        return response()->json(['message' => 'List of User Already Evaluated Category', 'data' => $evaluatedTypes->values()], 201);
    }

    // =================== Temporary yooo! =================
}
