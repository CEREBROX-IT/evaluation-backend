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

    public function getApproveComments(Request $request, $userid)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        $evaluationApprove = DB::table('evaluation')->join('users as evaluators', 'evaluation.user_id', '=', 'evaluators.id')->join('users as evaluated_users', 'evaluation.evaluated_id', '=', 'evaluated_users.id')->select('evaluation.id', 'evaluators.role as evaluator_role', 'evaluators.id as evaluator_id', 'evaluated_users.role as evaluated_role', 'evaluated_users.id as evaluated_id', 'evaluation.comment', 'evaluation.suggestion')->where('evaluated_users.id', $userid)->where('evaluation.status', true)->where('evaluation.approve_status', 'Approved')->orderBy('evaluation.id', 'desc')->get();

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
        $userid = $request->query('userid');

        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        $evaluated = EvaluationForm::where('evaluated_id', $userid)->exists();
        if (!$evaluated) {
            return response()->json(['error' => 'No one has evaluated this user yet'], 404);
        }

        $evaluationExists = EvaluationResult::where('status', true)->exists();

        if ($evaluationExists) {
            $ratingTotal = EvaluationResult::rightJoin('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')->where('evaluation.evaluated_id', $userid)->orderBy('rating')->groupBy('rating')->selectRaw('rating, count(evaluation_result.rating) as total')->pluck('total', 'rating');

            $ratingTotal = [
                '1' => $ratingTotal->get(1, 0),
                '2' => $ratingTotal->get(2, 0),
                '3' => $ratingTotal->get(3, 0),
                '4' => $ratingTotal->get(4, 0),
                '5' => $ratingTotal->get(5, 0),
            ];

            // Calculate overall rating
            $overallRating = $ratingTotal['5'] == 0 ? number_format(array_sum(array_keys($ratingTotal)) / 4, 3) : number_format(array_sum(array_keys($ratingTotal)) / 5, 3);
        } else {
            return response()->json(['message' => 'No evaluations available'], 200);
        }

        return response()->json(['message' => 'Overall Question Result (Bar Chart)', 'data' => $ratingTotal, 'overall_rating' => $overallRating], 201);
    }

    public function getQuestionRating(Request $request)
    {
        // Get the user ID from the request query parameters
        $userid = $request->query('userid');

        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')->where('evaluation.evaluated_id', $userid)->where('evaluation_result.status', true)->groupBy('question_id', 'question_description', 'type')->select('question_id', 'question_description', 'type')->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')->get();

        $ratingRange = [];

        foreach ($questionRatings as $result) {
            $ratings = [$result->{'1'}, $result->{'2'}, $result->{'3'}, $result->{'4'}, $result->{'5'}];
            $overallRatingScore = array_sum($ratings) / count($ratings); // Calculate the overall rating score
            $result->overall_rating_score = $overallRatingScore;

            // Find the maximum rating number that has a non-zero count
            $highestNonZeroRating = 0;
            foreach ($ratings as $index => $count) {
                if ($count > 0) {
                    $highestNonZeroRating = $index + 1;
                }
            }

            // Update the rating range based on the highest non-zero rating
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

        return response()->json(['message' => 'Pie chart per Question', 'data' => $formattedResults], 201);
    }

    public function officeServiceQuestionRating(Request $request)
    {
        // Get the user ID from the request query parameters
        $userid = $request->query('userid');

        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Retrieve question ratings for the user
        $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')->where('evaluation.evaluated_id', $userid)->where('evaluation_result.status', true)->where('evaluation.office_services', '!=', 'N/a')->groupBy('question_id', 'question_description', 'type')->select('question_id', 'question_description', 'type')->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')->get();

        $ratingRange = [];

        foreach ($questionRatings as $result) {
            $ratings = [$result->{'1'}, $result->{'2'}, $result->{'3'}, $result->{'4'}, $result->{'5'}];
            $overallRatingScore = array_sum($ratings) / count($ratings); // Calculate the overall rating score
            $result->overall_rating_score = $overallRatingScore;

            // Find the maximum rating number that has a non-zero count
            $highestNonZeroRating = 0;
            foreach ($ratings as $index => $count) {
                if ($count > 0) {
                    $highestNonZeroRating = $index + 1;
                }
            }

            // Update the rating range based on the highest non-zero rating
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

            // Iterate over the possible rating values
            foreach ($ratingRange as $rating) {
                // Check if the rating exists in the result, if not, set its count to 0
                $formattedResult[$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
            }

            $formattedResult['overall_rating_score'] = $result->overall_rating_score;
            // Add the formatted result to the final array
            $formattedResults[] = $formattedResult;
        }

        return response()->json(['message' => 'Pie chart per Question Office Services', 'data' => $formattedResults], 201);
    }

    // =================== Temporary yooo! =================

    // public function getEvaluationMasterList(Request $request)
    // {
    //     // Check if the request has valid authorization token
    //     $user = $this->authorizeRequest($request);
    //     if (!$user instanceof User) {
    //         return $user; // Return the response if authorization fails
    //     }

    //     // Retrieve evaluation results grouped by evaluated user and question
    //     $results = DB::table('evaluation')
    //         ->join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')
    //         ->where('evaluation.user_id', $user->id) // Filter by authenticated user as evaluator
    //         ->select('evaluation.evaluated_full_name as evaluated_name', 'evaluation_result.question_id', 'evaluation_result.rating')
    //         ->get();

    //     // Group results by evaluated user and calculate total rating for each question
    //     $evaluationResults = [];
    //     $maxQuestionId = 0;

    //     foreach ($results as $result) {
    //         $evaluatedName = $result->evaluated_name;
    //         $questionId = $result->question_id;
    //         $rating = (int) $result->rating;

    //         // Update max question ID
    //         if ($questionId > $maxQuestionId) {
    //             $maxQuestionId = $questionId;
    //         }

    //         // Initialize evaluated user if not present in the array
    //         if (!isset($evaluationResults[$evaluatedName])) {
    //             $evaluationResults[$evaluatedName] = ['evaluated_name' => $evaluatedName];
    //         }

    //         // Add or increment the rating for the question
    //         $evaluationResults[$evaluatedName]["Q$questionId"] = isset($evaluationResults[$evaluatedName]["Q$questionId"]) ? $evaluationResults[$evaluatedName]["Q$questionId"] + $rating : $rating;
    //     }

    //     // Fill missing questions with "N/a"
    //     foreach ($evaluationResults as &$result) {
    //         for ($i = 1; $i <= $maxQuestionId; $i++) {
    //             if (!isset($result["Q$i"])) {
    //                 $result["Q$i"] = 'N/a';
    //             }
    //         }
    //     }

    //     // Format the response
    //     $response = ['message' => 'Evaluation Result Master list', 'data' => array_values($evaluationResults)];

    //     return response()->json($response, 201);
    // }

    public function getEvaluationMasterList(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        $results = DB::table('evaluation')
            ->whereNotNull('evaluation.evaluated_id') // Exclude rows where evaluated_id is null
            ->where('evaluation.status', true)
            ->where('evaluation.office_services', 'N/a')
            ->join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')
            ->where('evaluation.user_id', $user->id)
            ->select('evaluation.evaluated_full_name as evaluated_name', 'evaluation_result.question_id', 'evaluation_result.rating')
            ->get();

        // Group results by evaluated user
        $evaluationResults = [];
        $maxQuestionId = 0;

        foreach ($results as $result) {
            $questionId = $result->question_id;
            $evaluatedName = $result->evaluated_name;

            // Initialize evaluated user if not present in the array
            if (!isset($evaluationResults[$evaluatedName])) {
                $evaluationResults[$evaluatedName] = ['evaluated_name' => $evaluatedName];
                $questionCount = 0; // Initialize question count for each evaluated user
            }

            $rating = (float) $result->rating;

            // Increment question count
            $questionCount++;

            // Update max question ID
            if ($questionCount > $maxQuestionId) {
                $maxQuestionId = $questionCount;
            }

            // Add or increment the rating for the question
            $evaluationResults[$evaluatedName]["Q$questionId"] = isset($evaluationResults[$evaluatedName]["Q$questionId"]) ? $evaluationResults[$evaluatedName]["Q$questionId"] + $rating : $rating;
        }

        // Fill missing questions with "N/a" for each evaluated user
        foreach ($evaluationResults as &$result) {
            for ($i = 1; $i <= $questionCount; $i++) {
                if (!isset($result["Q$i"])) {
                    $result["Q$i"] = 'N/a';
                }
            }
        }

        // Calculate overall rating per evaluated user and add it as the last column
        foreach ($evaluationResults as &$result) {
            $overallRating = 0;
            $totalCount = 0;
            foreach ($result as $key => $value) {
                if (strpos($key, 'Q') === 0 && $value !== 'N/a') {
                    $overallRating += $value;
                    $totalCount++;
                }
            }
            $overallRating = $totalCount > 0 ? number_format($overallRating / $totalCount, 3) : 'N/a';
            $result['Overall_Rating'] = $overallRating;
        }

        // Format the response
        $response = ['message' => 'Evaluation Result Master list', 'data' => array_values($evaluationResults)];

        return response()->json($response, 201);
    }

    public function getAverageRatingPerQuestion(Request $request)
    {
        // Retrieve all evaluation results
        $evaluationResults = DB::table('evaluation_result')->select('question_id', 'rating')->get();

        // Initialize an array to store the total rating and evaluator count for each question
        $questionRatings = [];

        // Calculate the total rating and evaluator count for each question
        foreach ($evaluationResults as $result) {
            $questionId = $result->question_id;
            $rating = (float) $result->rating;

            // Initialize the question in the array if not already present
            if (!isset($questionRatings[$questionId])) {
                $questionRatings[$questionId] = ['total_rating' => 0, 'evaluator_count' => 0];
            }

            // Increment the total rating and evaluator count for the question
            $questionRatings[$questionId]['total_rating'] += $rating;
            $questionRatings[$questionId]['evaluator_count']++;
        }

        // Calculate the average rating for each question and store total evaluators
        $averageRatings = [];
        $totalEvaluators = 0;
        foreach ($questionRatings as $questionId => $data) {
            $totalRating = $data['total_rating'];
            $evaluatorCount = $data['evaluator_count'];

            // Calculate the average rating, considering division by zero
            $averageRating = $evaluatorCount > 0 ? ($totalRating / $evaluatorCount) * 100 : 0;

            // Store the average rating for the question and update total evaluators
            $averageRatings["Q$questionId"] = number_format($averageRating, 3);
            $totalEvaluators += $evaluatorCount;
        }

        // Format the response
        $response = [
            'message' => 'Average Rating per Question (Percentage)',
            'data' => $averageRatings,
            'total_evaluators' => $totalEvaluators,
        ];

        return response()->json($response, 200);
    }

    public function getSummationRatingPerQuestion(Request $request)
    {
        $questions = DB::table('question')->where('status', true)->select('id')->get();

        // Get the IDs of questions with status true
        $questionIds = $questions->pluck('id')->toArray();
        $evaluationResults = DB::table('evaluation_result')
            ->whereIn('question_id', $questionIds)
            ->where('status', true) // Check if question rating status is true
            ->select('question_id', 'rating')
            ->get();

        $questionRatings = [];

        // Calculate the total rating for each question
        foreach ($evaluationResults as $result) {
            $questionId = $result->question_id;
            $rating = (float) $result->rating;

            // Initialize the question in the array if not already present
            if (!isset($questionRatings[$questionId])) {
                $questionRatings[$questionId] = ['total_rating' => 0];
            }

            // Add the rating to the total rating for the question
            $questionRatings[$questionId]['total_rating'] += $rating;
        }

        $totalQuestions = count($questionIds);
        $summationRatings = [];
        foreach ($questionRatings as $questionId => $data) {
            $totalRating = $data['total_rating'];

            $summationRating = $totalRating / $totalQuestions;
            $summationRating = number_format($summationRating, 3);
            $summationRatings["Q$questionId"] = $summationRating;
        }

        // Format the response
        $response = ['message' => 'Summation of question total of question', 'data' => $summationRatings];

        return response()->json($response, 200);
    }
}
