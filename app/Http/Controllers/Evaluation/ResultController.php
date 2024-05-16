<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Session;
use App\Models\EvaluationResult;
use App\Models\EvaluationForm;
use App\Models\Question;
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

    public function getApproveComments(Request $request, $userid)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        $evaluationApprove = EvaluationForm::join('users as evaluators', 'evaluation.user_id', '=', 'evaluators.id')
        ->join('users as evaluated_users', 'evaluation.evaluated_id', '=', 'evaluated_users.id')
        ->select('evaluation.id', 'evaluators.role as evaluator_role', 'evaluators.id as evaluator_id', 'evaluated_users.role as evaluated_role',
        'evaluated_users.id as evaluated_id', 'evaluation.comment', 'evaluation.suggestion')->where('evaluated_users.id', $userid)
        ->where('evaluation.status', true)->where('evaluation.approve_status', 'Approved')->orderBy('evaluation.id', 'desc')->get();

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

    public function getQuestionRating(Request $request)
{
    $userid = $request->query('userid'); // The user ID for whom the results are requested
    $type = $request->query('type'); // The type of question

    $user = $this->authorizeRequest($request);
    if (!$user instanceof User) {
        return $user;
    }

    $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id'
    )->where('evaluation.evaluated_id', $userid)->where('evaluation_result.type', $type)->where('evaluation.approve_status', "Approved")
    ->where('evaluation_result.status', true)->groupBy('question_id', 'question_description', 'type')
    ->select('question_id', 'question_description', 'type')->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')
    ->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')
    ->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')->get();

    $ratingRange = [];

    $evaluatorCount = EvaluationForm::join('users', 'evaluation.user_id', '=', 'users.id')
    ->join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')
    ->where('evaluation.evaluated_id', $userid)->where('evaluation.approve_status', "Approved")
    ->where('evaluation_result.type', $type)
    ->distinct('evaluation.user_id')
    ->count('evaluation.user_id');

    $totalOverallRatingScore = 0; // Initialize variable to store total overall_rating_score

    foreach ($questionRatings as $result) {
        $ratings = [$result->{'1'}, $result->{'2'}, $result->{'3'}, $result->{'4'}, $result->{'5'}];
        $overallRatingScore = array_sum($ratings) / $evaluatorCount;
        $result->overall_rating_score = number_format($overallRatingScore, 2); // Format to two decimal places

        $highestNonZeroRating = 0;
        foreach ($ratings as $index => $count) {
            if ($count > 0) {
                $highestNonZeroRating = $index + 1;
            }
        }

        for ($i = 1; $i <= $highestNonZeroRating; $i++) {
            $ratingRange[] = (string) $i;
        }

        $totalOverallRatingScore += $overallRatingScore; // Add overall_rating_score to total
    }

    // Calculate average overall rating score and format to two decimal places
    $averageOverallRatingScore = number_format($totalOverallRatingScore / count($questionRatings), 2);

    // Organize the results into the desired format
    $formattedResults = [];
    foreach ($questionRatings as $result) {
        $formattedResult = [
            'id' => $result->question_id,
            'type' => $result->type,
            'question_description' => $result->question_description,
        ];

        // Check if the highest rating in the range is greater than or equal to 4
        if (max($ratingRange) >= 4) {
            for ($rating = 1; $rating <= 5; $rating++) {
                $formattedResult[$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
            }
        } else {
            for ($rating = 1; $rating <= 4; $rating++) {
                $formattedResult[$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
            }
        }

        $formattedResult['overall_rating_score'] = $result->overall_rating_score;
        // Add the formatted result to the final array
        $formattedResults[] = $formattedResult;
    }

    return response()->json(['message' => 'Pie chart per Question', 'pie_chart' => $formattedResults, 'evaluator_count'
    => $evaluatorCount, 'average_overall_rating_score' => $averageOverallRatingScore], 201);
}



    public function officeServiceQuestionRating(Request $request)
    {
        $userid = $request->query('userid');

        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        // Retrieve question ratings for the user
        $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
        ->where('evaluation.evaluated_id', $userid)->where('evaluation_result.status', true)->where('evaluation.office_services', '!=', 'N/a')
        ->groupBy('question_id', 'question_description', 'type')->select('question_id', 'question_description', 'type')
        ->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')
        ->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')
        ->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')->get();

        $ratingRange = [];

        foreach ($questionRatings as $result) {
            $ratings = [$result->{'1'}, $result->{'2'}, $result->{'3'}, $result->{'4'}, $result->{'5'}];
            $overallRatingScore = array_sum($ratings) / count($ratings);
            $result->overall_rating_score = $overallRatingScore;

            // Find the maximum rating number that has a non-zero count
            $highestNonZeroRating = 0;
            foreach ($ratings as $index => $count) {
                if ($count > 0) {
                    $highestNonZeroRating = $index + 1;
                }
            }

            for ($i = 1; $i <= $highestNonZeroRating; $i++) {
                $ratingRange[] = (string) $i;
            }
        }

        // Organize the results into the desired format
        $formattedResults = [];
        foreach ($questionRatings as $result) {
            $formattedResult = [
                'id' => $result->question_id,
                'type' => $result->type,
                'question_description' => $result->question_description,
            ];

            foreach ($ratingRange as $rating) {
                $formattedResult[$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
            }

            $formattedResult['overall_rating_score'] = $result->overall_rating_score;
            $formattedResults[] = $formattedResult;
        }

        return response()->json(['message' => 'Pie chart per Question Office Services', 'data' => $formattedResults], 201);
    }

    // public function getEvaluationMasterList(Request $request)
    // {
    //     $user = $this->authorizeRequest($request);
    //     if (!$user instanceof User) {
    //         return $user;
    //     }

    //     $results = EvaluationForm::whereNotNull('evaluation.evaluated_id')->where('evaluation.status', true)->where('evaluation.office_services', 'N/a')->join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')->select('evaluation.evaluated_full_name as evaluated_name', 'evaluation.evaluated_id', 'evaluation_result.question_id', 'evaluation_result.rating')->get();

    //     $evaluationResults = [];
    //     $maxQuestionId = 0;

    //     foreach ($results as $result) {
    //         $evaluatedId = $result->evaluated_id;
    //         $evaluatedName = $result->evaluated_name;
    //         $questionId = $result->question_id;
    //         $rating = (int) $result->rating;

    //         // Update max question ID
    //         if ($questionId > $maxQuestionId) {
    //             $maxQuestionId = $questionId;
    //         }

    //         if (!isset($evaluationResults[$evaluatedId])) {
    //             $evaluationResults[$evaluatedId] = ['evaluated_id' => $evaluatedId, 'evaluated_name' => $evaluatedName];
    //         }

    //         // Add or increment the rating for the question
    //         $questionKey = "Q$questionId";
    //         $evaluationResults[$evaluatedId][$questionKey] = isset($evaluationResults[$evaluatedId][$questionKey]) ? $evaluationResults[$evaluatedId][$questionKey] + $rating : $rating;
    //     }

    //     // Ensure that every evaluated user's data includes all possible question IDs
    //     foreach ($evaluationResults as &$result) {
    //         $reindexedResult = [];
    //         for ($i = 1; $i <= $maxQuestionId; $i++) {
    //             $questionKey = "Q$i";
    //             $reindexedResult[$questionKey] = isset($result[$questionKey]) ? $result[$questionKey] : 'N/a';
    //         }
    //         $result = $reindexedResult;

    //         // Calculate overall rating per evaluated user and add it as the last column
    //         $overallRating = 0;
    //         $totalCount = 0;
    //         foreach ($result as $key => $value) {
    //             if (strpos($key, 'Q') === 0 && $value !== 'N/a') {
    //                 $overallRating += $value;
    //                 $totalCount++;
    //             }
    //         }
    //         $overallRating = $totalCount > 0 ? number_format($overallRating / $totalCount, 3) : 'N/a';
    //         $result['Overall_Rating'] = $overallRating;
    //     }

    //     // Format the response
    //     $response = ['message' => 'Evaluation Result Master list', 'data' => array_values($evaluationResults)];

    //     return response()->json($response, 201);
    // }
}
