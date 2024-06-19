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
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }

        // Update the user's last evaluated school year
        $userEvaluation = User::where('id', $request->user_id)->update(['last_evaluated' => $request->school_year]);

        $evaluatedFullName = $request->evaluated_full_name;
        // Create an Evaluation instance
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
        return response()->json(['message' => 'Evaluation and EvaluationResult created successfully', 'evaluation' => $evaluation], 201);
    }

    // ================= Update Evaluation Result =================
    public function updateEvaluationRating(Request $request, $id)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
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

        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        $result = EvaluationResult::find($id);

        if ($result) {
            return response()->json(['message' => ' Evaluation result not found'], 404);
        }
        $result = update(['status' => false]);
        return response()->json(['message' => ' Evaluation result deleted successfully', 'data' => $result], 201);
    }




//     public function getEvaluationMasterList(Request $request)
// {
//     $user = $this->authorizeRequest($request);
//     if (!$user instanceof User) {
//         return $user;
//     }

//     $questionType = $request->input('evaluationType');
//     $masterlist = [];

//     try {
//         // Get all distinct evaluated_ids with approved evaluations
//         $evaluatedIds = EvaluationForm::join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')
//             ->where('evaluation.approve_status', 'Approved')
//             ->where('evaluation.status', true)
//             ->distinct('evaluation.evaluated_id')
//             ->pluck('evaluation.evaluated_id');

//         // Loop through each evaluated user
//         foreach ($evaluatedIds as $evaluatedId) {

//             $evaluatedUser = User::find($evaluatedId);
//             if (!$evaluatedUser) {
//                 continue;
//             }

//             // Get all evaluations for this evaluated user
//             $evaluationsQuery = EvaluationForm::join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')
//                 ->where('evaluation.evaluated_id', $evaluatedId)
//                 ->where('evaluation.approve_status', 'Approved')
//                 ->where('evaluation.status', true)
//                 ->select('evaluation_result.*', 'evaluation.user_id', 'users.role as evaluator_role')
//                 ->join('users', 'evaluation.user_id', '=', 'users.id');

//             // Filter evaluations by question type if provided in the request
//             if ($questionType) {
//                 $evaluationsQuery->where('evaluation_result.type', $questionType);
//             }

//             $evaluations = $evaluationsQuery->get();

//             // Check if there are evaluations for this user
//             if ($evaluations->isEmpty()) {
//                 continue;
//             }

//             // Initialize an array to store the total rating and count of evaluators for each question
//             $ratings = [];
//             $totalRatingsSum = 0;
//             $totalWeightSum = 0;
//             $evaluatorCounts = [];
//             $evaluators = [];

//             // Loop through each evaluation
//             foreach ($evaluations as $evaluation) {
//                 $role = $evaluation->evaluator_role;
//                 $rolePercentage = $this->getRolePercentage($role);

//                 if (!isset($ratings[$evaluation->question_description])) {
//                     $ratings[$evaluation->question_description] = [
//                         'total_weighted_rating' => 0,
//                         'total_weight' => 0,
//                         'ratings' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
//                     ];
//                 }

//                 // Increment total weighted rating and total weight for this question
//                 $ratings[$evaluation->question_description]['total_weighted_rating'] += $evaluation->rating * $rolePercentage;
//                 $ratings[$evaluation->question_description]['total_weight'] += $rolePercentage;
//                 $ratings[$evaluation->question_description]['ratings'][$evaluation->rating]++;

//                 // Add to the overall total ratings sum and total weight sum
//                 $totalRatingsSum += $evaluation->rating * $rolePercentage;
//                 $totalWeightSum += $rolePercentage;

//                 // Track the evaluator count for each role
//                 if (!isset($evaluatorCounts[$role])) {
//                     $evaluatorCounts[$role] = 0;
//                 }
//                 $evaluatorCounts[$role]++;

//                 // Track unique evaluators
//                 if (!in_array($evaluation->user_id, $evaluators)) {
//                     $evaluators[] = $evaluation->user_id;
//                 }
//             }

//             // Calculate the average rating for each question
//             foreach ($ratings as $questionDescription => $ratingData) {
//                 $averageRating = $ratingData['total_weight'] > 0
//                     ? round($ratingData['total_weighted_rating'] / $ratingData['total_weight'], 2)
//                     : 0;
//                 $ratings[$questionDescription]['average_rating'] = number_format($averageRating, 2);
//             }

//             // Calculate the overall average rating for the evaluated user
//             $overallAverageRating = $totalWeightSum > 0
//                 ? round($totalRatingsSum / $totalWeightSum, 2)
//                 : 0;

//             // Add the evaluated user data to the master list
//             $masterlist[] = [
//                 'evaluated_id' => $evaluatedUser->id,
//                 'evaluated_name' => $evaluatedUser->first_name . ' ' . $evaluatedUser->last_name,
//                 'ratings' => $ratings,
//                 'overall_average_rating' => number_format($overallAverageRating, 2),
//                 'evaluator_count' => count($evaluators),
//                 'evaluator_roles' => $evaluatorCounts
//             ];
//         }

//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }

//     return response()->json(['data' => $masterlist], 200);
// }

// private function getRolePercentage($role)
// {
//     // Define role percentages. Adjust these values as per your requirements.
//     $rolePercentages = [
//         'Principal' => 0.5,
//         'Teacher' => 0.3,
//         'Student' => 0.2,
//         // Add more roles as needed
//     ];

//     return $rolePercentages[$role] ?? 0.1; // Default to 0.1 if role not found
// }



// public function getEvaluationMasterList(Request $request)
// {
//     $user = $this->authorizeRequest($request);
//     if (!$user instanceof User) {
//         return $user;
//     }

//     $types = ['VALUATION OF TEACHERS PERFORMANCE', 'STUDENT EVALUATION OF TEACHING'];

//     // Fetch the ratings with roles and group by evaluated_id and evaluator_role
//     $evaluations = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
//         ->join('users as evaluators', 'evaluation.user_id', '=', 'evaluators.id')
//         ->join('users as evaluated', 'evaluation.evaluated_id', '=', 'evaluated.id')
//         ->whereIn('evaluation_result.type', $types)
//         ->where('evaluation.approve_status', "Approved")
//         ->where('evaluation_result.status', true)
//         ->groupBy('evaluation.evaluated_id', 'evaluation_result.question_id', 'evaluation_result.question_description', 'evaluation_result.type', 'evaluators.role', 'evaluated.role')
//         ->select('evaluation.evaluated_id', \DB::raw('CONCAT(evaluated.first_name, " ", evaluated.last_name) as evaluated_name'), 'evaluation_result.question_id', 'evaluation_result.question_description', 'evaluation_result.type', 'evaluators.role as evaluator_role', 'evaluated.role as evaluated_role')
//         ->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')
//         ->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')
//         ->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')
//         ->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')
//         ->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')
//         ->get();

//     if (count($evaluations) === 0) {
//         return response()->json(['message' => 'No one evaluated yet'], 200);
//     }

//     $rolePercentages = [
//         'Principal' => 0.50,
//         'Student' => 0.30,
//         'Teacher' => 0.20,
//     ];

//     $masterlist = [];

//     foreach ($evaluations as $result) {
//         $evaluatedId = $result->evaluated_id;
//         $questionId = $result->question_id;

//         if (!isset($masterlist[$evaluatedId])) {
//             $masterlist[$evaluatedId] = [
//                 'evaluated_id' => $evaluatedId,
//                 'evaluated_name' => $result->evaluated_name,
//                 'results' => []
//             ];
//         }

//         $ratings = [
//             1 => $result->{'1'},
//             2 => $result->{'2'},
//             3 => $result->{'3'},
//             4 => $result->{'4'},
//             5 => $result->{'5'},
//         ];

//         $role = $result->evaluator_role;
//         $weight = isset($rolePercentages[$role]) ? $rolePercentages[$role] : 0;

//         $overallRatingScore = 0;
//         $totalWeight = 0;

//         foreach ($ratings as $rating => $count) {
//             $overallRatingScore += $rating * $count * $weight;
//             $totalWeight += $count * $weight;
//         }

//         $overallRatingScore = $totalWeight > 0 ? $overallRatingScore / $totalWeight : 0;
//         $result->overall_rating_score = number_format($overallRatingScore, 2);

//         $masterlist[$evaluatedId]['results'][] = [
//             'id' => $questionId,
//             'type' => $result->type,
//             'question_description' => $result->question_description,
//             'ratings' => [
//                 '1' => $result->{'1'},
//                 '2' => $result->{'2'},
//                 '3' => $result->{'3'},
//                 '4' => $result->{'4'},
//                 '5' => $result->{'5'},
//             ],
//             'overall_rating_score' => $result->overall_rating_score,
//             'evaluator_role' => $result->evaluator_role,
//             'evaluated_role' => $result->evaluated_role
//         ];
//     }

//     return response()->json([
//         'message' => 'Masterlist Average rating',
//         'data' => $masterlist
//     ], 201);
// }


// Final Revision for the Teachers Evaluation Masterlist
public function getEvaluationMasterList(Request $request)
{
    $user = $this->authorizeRequest($request);
    if (!$user instanceof User) {
        return $user;
    }

    $types = ['VALUATION OF TEACHERS PERFORMANCE', 'STUDENT EVALUATION OF TEACHING'];

    // Fetch the ratings with roles and group by evaluated_id and evaluator_role
    $evaluations = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
        ->join('users as evaluators', 'evaluation.user_id', '=', 'evaluators.id')
        ->join('users as evaluated', 'evaluation.evaluated_id', '=', 'evaluated.id')
        ->whereIn('evaluation_result.type', $types)
        ->where('evaluation.approve_status', "Approved")
        ->where('evaluation_result.status', true)
        ->groupBy('evaluation.evaluated_id', 'evaluation_result.question_id', 'evaluation_result.question_description', 'evaluation_result.type', 'evaluators.role', 'evaluated.role')
        ->select('evaluation.evaluated_id', \DB::raw('CONCAT(evaluated.first_name, " ", evaluated.last_name) as evaluated_name'), 'evaluation_result.question_id', 'evaluation_result.question_description', 'evaluation_result.type', 'evaluators.role as evaluator_role', 'evaluated.role as evaluated_role')
        ->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')
        ->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')
        ->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')
        ->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')
        ->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')
        ->get();

    if (count($evaluations) === 0) {
        return response()->json(['message' => 'No one evaluated yet'], 200);
    }

    $adminRoles = ['Principal', 'Treasurer', 'Registrar', 'Coordinator'];
    $rolePercentages = [
        'Admin' => 0.50,
        'Student' => 0.30,
        'Teacher' => 0.20,
    ];

    $masterlist = [];
    $overallRatingScores = [];

    foreach ($evaluations as $result) {
        $evaluatedId = $result->evaluated_id;
        $questionId = $result->question_id;

        if (!isset($masterlist[$evaluatedId])) {
            $masterlist[$evaluatedId] = [
                'evaluated_id' => $evaluatedId,
                'evaluated_name' => $result->evaluated_name,
                'results' => []
            ];
        }

        $ratings = [
            1 => $result->{'1'},
            2 => $result->{'2'},
            3 => $result->{'3'},
            4 => $result->{'4'},
            5 => $result->{'5'},
        ];

        $role = in_array($result->evaluator_role, $adminRoles) ? 'Admin' : $result->evaluator_role;
        $weight = isset($rolePercentages[$role]) ? $rolePercentages[$role] : 0;

        $overallRatingScore = 0;
        $totalWeight = 0;

        foreach ($ratings as $rating => $count) {
            $overallRatingScore += $rating * $count * $weight;
            $totalWeight += $count * $weight;
        }

        $overallRatingScore = $totalWeight > 0 ? $overallRatingScore / $totalWeight : 0;
        $result->overall_rating_score = number_format($overallRatingScore, 2);

        // Collect overall rating scores for averaging later
        $overallRatingScores[] = $overallRatingScore;

        $masterlist[$evaluatedId]['results'][] = [
            'id' => $questionId,
            'type' => $result->type,
            'question_description' => $result->question_description,
            'ratings' => [
                '1' => $result->{'1'},
                '2' => $result->{'2'},
                '3' => $result->{'3'},
                '4' => $result->{'4'},
                '5' => $result->{'5'},
            ],
            'overall_rating_score' => $result->overall_rating_score,
            'evaluator_role' => $result->evaluator_role,
            'evaluated_role' => $result->evaluated_role
        ];
    }

    // Calculate the average overall rating score
    $averageOverallRatingScore = count($overallRatingScores) > 0
        ? array_sum($overallRatingScores) / count($overallRatingScores)
        : 0;

    return response()->json([
        'message' => 'Masterlist Average rating',
        'data' => $masterlist,
        'average_overall_rating_score' => number_format($averageOverallRatingScore, 2)
    ], 201);
}


// Office Service Rating Masterlist
public function OSAverageRatingMasterlist(Request $request)
{ $user = $this->authorizeRequest($request);
    if (!$user instanceof User) {
        return $user;
    }

    $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
        ->where('evaluation.approve_status', 'Approved')
        ->where('evaluation_result.status', true)
        ->where('evaluation_result.type', 'EVALUATION INSTRUMENT FOR CUSTOMER')
        ->groupBy('evaluation.evaluated_id', 'question_id', 'question_description', 'type')
        ->select('evaluation.evaluated_id', 'question_id', 'question_description', 'type')
        ->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')
        ->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')
        ->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')
        ->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')
        ->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')
        ->get();

    $resultsByUser = $questionRatings->groupBy('evaluated_id');

    $finalResults = [];
    $totalOverallRatingScore = 0;
    $totalQuestions = 0;

    foreach ($resultsByUser as $evaluatedId => $ratings) {
        $evaluatedName = User::where('id', $evaluatedId)->value(DB::raw("CONCAT(first_name, ' ', last_name)"));

        $evaluatorCount = EvaluationForm::where('evaluated_id', $evaluatedId)
            ->where('approve_status', 'Approved')
            ->distinct('user_id')
            ->count('user_id');

        $userTotalRatingScore = 0;

        foreach ($ratings as $result) {
            $ratingsCount = [
                1 => $result->{'1'},
                2 => $result->{'2'},
                3 => $result->{'3'},
                4 => $result->{'4'},
                5 => $result->{'5'},
            ];

            $overallRatingScore = 0;
            foreach ($ratingsCount as $rating => $count) {
                $overallRatingScore += $rating * $count;
            }

            if ($evaluatorCount > 0) {
                $overallRatingScore /= $evaluatorCount;
                $result->overall_rating_score = number_format($overallRatingScore, 2);
                $userTotalRatingScore += $overallRatingScore;
            } else {
                $result->overall_rating_score = null;
            }

            $highestNonZeroRating = 0;
            foreach ($ratingsCount as $index => $count) {
                if ($count > 0) {
                    $highestNonZeroRating = $index;
                }
            }

            $ratingRange = [];
            for ($i = 1; $i <= $highestNonZeroRating; $i++) {
                $ratingRange[] = (string) $i;
            }

            $formattedResult = [
                'id' => $result->question_id,
                'type' => $result->type,
                'question_description' => $result->question_description,
                'ratings' => []
            ];

            if (max($ratingRange) >= 4) {
                for ($rating = 1; $rating <= 5; $rating++) {
                    $formattedResult['ratings'][$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
                }
            } else {
                for ($rating = 1; $rating <= 4; $rating++) {
                    $formattedResult['ratings'][$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
                }
            }

            $formattedResult['overall_rating_score'] = $result->overall_rating_score;

            $evaluatorRole = EvaluationForm::join('users', 'evaluation.user_id', '=', 'users.id')
                ->where('evaluation.evaluated_id', $evaluatedId)
                ->value('users.role');

            $evaluatedRole = User::where('id', $evaluatedId)->value('role');

            $formattedResult['evaluator_role'] = $evaluatorRole;
            $formattedResult['evaluated_role'] = $evaluatedRole;

            $finalResults[$evaluatedId]['evaluated_id'] = $evaluatedId;
            $finalResults[$evaluatedId]['evaluated_name'] = $evaluatedName;
            $finalResults[$evaluatedId]['results'][] = $formattedResult;

        }

        if ($evaluatorCount > 0) {
            $averageOverallRatingScore = number_format($userTotalRatingScore / count($ratings), 2);
        } else {
            $averageOverallRatingScore = null;
        }

        $finalResults[$evaluatedId]['average_overall_rating_score'] = $averageOverallRatingScore;
        $finalResults[$evaluatedId]['evaluator_count'] = $evaluatorCount;

        $totalOverallRatingScore += $userTotalRatingScore;
        $totalQuestions += count($ratings);
    }

    if ($totalQuestions > 0) {
        $overallAverageRatingScore = number_format($totalOverallRatingScore / $totalQuestions, 2);
    } else {
        $overallAverageRatingScore = null;
    }

    $response = [
        'message' => 'Office Services Masterlist Average rating',
        'data' => empty($finalResults) ? 'No one evaluated yet' : $finalResults,
        'average_overall_rating_score' => $overallAverageRatingScore
    ];

    return response()->json($response, 201);
}


// Admin Evaluation Masterist
public function AdminEvalAverageRatingMasterlist(Request $request)
{ $user = $this->authorizeRequest($request);
    if (!$user instanceof User) {
        return $user;
    }

    $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
        ->where('evaluation.approve_status', 'Approved')
        ->where('evaluation_result.status', true)
        ->where('evaluation_result.type', 'ADMINISTRATORS EVALUATION')
        ->groupBy('evaluation.evaluated_id', 'question_id', 'question_description', 'type')
        ->select('evaluation.evaluated_id', 'question_id', 'question_description', 'type')
        ->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')
        ->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')
        ->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')
        ->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')
        ->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')
        ->get();

    $resultsByUser = $questionRatings->groupBy('evaluated_id');

    $finalResults = [];
    $totalOverallRatingScore = 0;
    $totalQuestions = 0;

    foreach ($resultsByUser as $evaluatedId => $ratings) {
        $evaluatedName = User::where('id', $evaluatedId)->value(DB::raw("CONCAT(first_name, ' ', last_name)"));

        $evaluatorCount = EvaluationForm::where('evaluated_id', $evaluatedId)
            ->where('approve_status', 'Approved')
            ->distinct('user_id')
            ->count('user_id');

        $userTotalRatingScore = 0;

        foreach ($ratings as $result) {
            $ratingsCount = [
                1 => $result->{'1'},
                2 => $result->{'2'},
                3 => $result->{'3'},
                4 => $result->{'4'},
                5 => $result->{'5'},
            ];

            $overallRatingScore = 0;
            foreach ($ratingsCount as $rating => $count) {
                $overallRatingScore += $rating * $count;
            }

            if ($evaluatorCount > 0) {
                $overallRatingScore /= $evaluatorCount;
                $result->overall_rating_score = number_format($overallRatingScore, 2);
                $userTotalRatingScore += $overallRatingScore;
            } else {
                $result->overall_rating_score = null;
            }

            $highestNonZeroRating = 0;
            foreach ($ratingsCount as $index => $count) {
                if ($count > 0) {
                    $highestNonZeroRating = $index;
                }
            }

            $ratingRange = [];
            for ($i = 1; $i <= $highestNonZeroRating; $i++) {
                $ratingRange[] = (string) $i;
            }

            $formattedResult = [
                'id' => $result->question_id,
                'type' => $result->type,
                'question_description' => $result->question_description,
                'ratings' => []
            ];

            if (max($ratingRange) >= 4) {
                for ($rating = 1; $rating <= 5; $rating++) {
                    $formattedResult['ratings'][$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
                }
            } else {
                for ($rating = 1; $rating <= 4; $rating++) {
                    $formattedResult['ratings'][$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
                }
            }

            $formattedResult['overall_rating_score'] = $result->overall_rating_score;

            $evaluatorRole = EvaluationForm::join('users', 'evaluation.user_id', '=', 'users.id')
                ->where('evaluation.evaluated_id', $evaluatedId)
                ->value('users.role');

            $evaluatedRole = User::where('id', $evaluatedId)->value('role');

            $formattedResult['evaluator_role'] = $evaluatorRole;
            $formattedResult['evaluated_role'] = $evaluatedRole;

            $finalResults[$evaluatedId]['evaluated_id'] = $evaluatedId;
            $finalResults[$evaluatedId]['evaluated_name'] = $evaluatedName;
            $finalResults[$evaluatedId]['results'][] = $formattedResult;

        }

        if ($evaluatorCount > 0) {
            $averageOverallRatingScore = number_format($userTotalRatingScore / count($ratings), 2);
        } else {
            $averageOverallRatingScore = null;
        }

        $finalResults[$evaluatedId]['average_overall_rating_score'] = $averageOverallRatingScore;
        $finalResults[$evaluatedId]['evaluator_count'] = $evaluatorCount;

        $totalOverallRatingScore += $userTotalRatingScore;
        $totalQuestions += count($ratings);
    }

    if ($totalQuestions > 0) {
        $overallAverageRatingScore = number_format($totalOverallRatingScore / $totalQuestions, 2);
    } else {
        $overallAverageRatingScore = null;
    }

    $response = [
        'message' => 'Administrator Evaluation Masterlist Average rating',
        'data' => empty($finalResults) ? 'No one evaluated yet' : $finalResults,
        'average_overall_rating_score' => $overallAverageRatingScore
    ];

    return response()->json($response, 201);
}



public function getQuestionRating(Request $request)
{
    $userid = $request->query('userid');
    $type = $request->query('type');

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

    $totalOverallRatingScore = 0;

    foreach ($questionRatings as $result) {
        $ratings = [
            1 => $result->{'1'},
            2 => $result->{'2'},
            3 => $result->{'3'},
            4 => $result->{'4'},
            5 => $result->{'5'},
        ];

        $overallRatingScore = 0;
        foreach ($ratings as $rating => $count) {
            $overallRatingScore += $rating * $count;
        }
        $overallRatingScore /= $evaluatorCount;

        $result->overall_rating_score = number_format($overallRatingScore, 2);

        $highestNonZeroRating = 0;
        foreach ($ratings as $index => $count) {
            if ($count > 0) {
                $highestNonZeroRating = $index + 1;
            }
        }

        for ($i = 1; $i <= $highestNonZeroRating; $i++) {
            $ratingRange[] = (string) $i;
        }

        $totalOverallRatingScore += $overallRatingScore;
    }

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




// finalize correct
// public function TeachergetQuestionRating(Request $request)
// {
//     $userid = $request->query('userid');

//     $user = $this->authorizeRequest($request);
//     if (!$user instanceof User) {
//         return $user;
//     }

//     $types = ['VALUATION OF TEACHERS PERFORMANCE', 'STUDENT EVALUATION OF TEACHING'];

//     // Fetch the ratings with roles
//     $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
//         ->join('users', 'evaluation.user_id', '=', 'users.id')
//         ->where('evaluation.evaluated_id', $userid)
//         ->whereIn('evaluation_result.type', $types)
//         ->where('evaluation.approve_status', "Approved")
//         ->where('evaluation_result.status', true)
//         ->groupBy('question_id', 'question_description', 'type', 'users.role')  // Include users.role in the GROUP BY clause
//         ->select('question_id', 'question_description', 'type', 'users.role as evaluator_role')
//         ->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')
//         ->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')
//         ->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')
//         ->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')
//         ->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')
//         ->get();

//     $evaluatorCount = EvaluationForm::join('users', 'evaluation.user_id', '=', 'users.id')
//         ->join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')
//         ->where('evaluation.evaluated_id', $userid)
//         ->where('evaluation.approve_status', "Approved")
//         ->whereIn('evaluation_result.type', $types)
//         ->distinct('evaluation.user_id')
//         ->count('evaluation.user_id');

//     if ($evaluatorCount === 0 || count($questionRatings) === 0) {
//         return response()->json(['message' => 'No one evaluated yet'], 200);
//     }

//     $rolePercentages = [
//         'Principal' => 0.50,
//         'Student' => 0.30,
//         'Teacher' => 0.20,
//     ];

//     $totalOverallRatingScore = 0;

//     // To store the sum of weighted scores and the total weight
//     $weightedScores = [];

//     foreach ($questionRatings as $result) {
//         $ratings = [
//             1 => $result->{'1'},
//             2 => $result->{'2'},
//             3 => $result->{'3'},
//             4 => $result->{'4'},
//             5 => $result->{'5'},
//         ];

//         $role = $result->evaluator_role;
//         $weight = isset($rolePercentages[$role]) ? $rolePercentages[$role] : 0;

//         $overallRatingScore = 0;
//         $totalWeight = 0;

//         foreach ($ratings as $rating => $count) {
//             $overallRatingScore += $rating * $count * $weight;
//             $totalWeight += $count * $weight;
//         }

//         $overallRatingScore = $totalWeight > 0 ? $overallRatingScore / $totalWeight : 0;

//         // Summing up the scores and weights for later averaging
//         if (!isset($weightedScores[$result->question_id])) {
//             $weightedScores[$result->question_id] = ['score' => 0, 'weight' => 0];
//         }
//         $weightedScores[$result->question_id]['score'] += $overallRatingScore * $totalWeight;
//         $weightedScores[$result->question_id]['weight'] += $totalWeight;

//         $result->overall_rating_score = number_format($overallRatingScore, 2);
//     }

//     foreach ($questionRatings as $result) {
//         $totalOverallRatingScore += $result->overall_rating_score;
//     }

//     $averageOverallRatingScore = number_format($totalOverallRatingScore / count($questionRatings), 2);

//     // Organize the results into the desired format
//     $formattedResults = [];
//     foreach ($questionRatings as $result) {
//         $formattedResult = [
//             'id' => $result->question_id,
//             'type' => $result->type,
//             'question_description' => $result->question_description,
//         ];

//         foreach (range(1, 5) as $rating) {
//             $formattedResult[$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
//         }

//         $formattedResult['overall_rating_score'] = $result->overall_rating_score;
//         $formattedResults[] = $formattedResult;
//     }

//     return response()->json([
//         'message' => 'Pie chart per Question',
//         'pie_chart' => $formattedResults,
//         'evaluator_count' => $evaluatorCount,
//         'average_overall_rating_score' => $averageOverallRatingScore
//     ], 201);
// }

// Teacher Evaluation Result Rating
public function TeachergetQuestionRating(Request $request)
{
    $userid = $request->query('userid');

    $user = $this->authorizeRequest($request);
    if (!$user instanceof User) {
        return $user;
    }

    $types = ['VALUATION OF TEACHERS PERFORMANCE', 'STUDENT EVALUATION OF TEACHING'];

    // Fetch the ratings with roles
    $questionRatings = EvaluationResult::join('evaluation', 'evaluation_result.evaluation_id', '=', 'evaluation.id')
        ->join('users', 'evaluation.user_id', '=', 'users.id')
        ->where('evaluation.evaluated_id', $userid)
        ->whereIn('evaluation_result.type', $types)
        ->where('evaluation.approve_status', "Approved")
        ->where('evaluation_result.status', true)
        ->groupBy('question_id', 'question_description', 'type', 'users.role')  // Include users.role in the GROUP BY clause
        ->select('question_id', 'question_description', 'type', 'users.role as evaluator_role')
        ->selectRaw('sum(case when rating = 1 then 1 else 0 end) as "1"')
        ->selectRaw('sum(case when rating = 2 then 1 else 0 end) as "2"')
        ->selectRaw('sum(case when rating = 3 then 1 else 0 end) as "3"')
        ->selectRaw('sum(case when rating = 4 then 1 else 0 end) as "4"')
        ->selectRaw('sum(case when rating = 5 then 1 else 0 end) as "5"')
        ->get();

    $evaluatorCount = EvaluationForm::join('users', 'evaluation.user_id', '=', 'users.id')
        ->join('evaluation_result', 'evaluation.id', '=', 'evaluation_result.evaluation_id')
        ->where('evaluation.evaluated_id', $userid)
        ->where('evaluation.approve_status', "Approved")
        ->whereIn('evaluation_result.type', $types)
        ->distinct('evaluation.user_id')
        ->count('evaluation.user_id');

    if ($evaluatorCount === 0 || count($questionRatings) === 0) {
        return response()->json(['message' => 'No one evaluated yet'], 200);
    }

    $adminRoles = ['Principal', 'Treasurer', 'Registrar', 'Coordinator'];
    $rolePercentages = [
        'Admin' => 0.50,
        'Student' => 0.30,
        'Teacher' => 0.20,
    ];

    $totalOverallRatingScore = 0;

    // To store the sum of weighted scores and the total weight
    $weightedScores = [];

    foreach ($questionRatings as $result) {
        $ratings = [
            1 => $result->{'1'},
            2 => $result->{'2'},
            3 => $result->{'3'},
            4 => $result->{'4'},
            5 => $result->{'5'},
        ];

        $role = in_array($result->evaluator_role, $adminRoles) ? 'Admin' : $result->evaluator_role;
        $weight = isset($rolePercentages[$role]) ? $rolePercentages[$role] : 0;

        $overallRatingScore = 0;
        $totalWeight = 0;

        foreach ($ratings as $rating => $count) {
            $overallRatingScore += $rating * $count * $weight;
            $totalWeight += $count * $weight;
        }

        $overallRatingScore = $totalWeight > 0 ? $overallRatingScore / $totalWeight : 0;

        // Summing up the scores and weights for later averaging
        if (!isset($weightedScores[$result->question_id])) {
            $weightedScores[$result->question_id] = ['score' => 0, 'weight' => 0];
        }
        $weightedScores[$result->question_id]['score'] += $overallRatingScore * $totalWeight;
        $weightedScores[$result->question_id]['weight'] += $totalWeight;

        $result->overall_rating_score = number_format($overallRatingScore, 2);
    }

    foreach ($questionRatings as $result) {
        $totalOverallRatingScore += $result->overall_rating_score;
    }

    $averageOverallRatingScore = number_format($totalOverallRatingScore / count($questionRatings), 2);

    // Organize the results into the desired format
    $formattedResults = [];
    foreach ($questionRatings as $result) {
        $formattedResult = [
            'id' => $result->question_id,
            'type' => $result->type,
            'question_description' => $result->question_description,
        ];

        foreach (range(1, 5) as $rating) {
            $formattedResult[$rating] = isset($result->{$rating}) ? $result->{$rating} : 0;
        }

        $formattedResult['overall_rating_score'] = $result->overall_rating_score;
        $formattedResults[] = $formattedResult;
    }

    return response()->json([
        'message' => 'Pie chart per Question',
        'pie_chart' => $formattedResults,
        'evaluator_count' => $evaluatorCount,
        'average_overall_rating_score' => $averageOverallRatingScore
    ], 201);
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
