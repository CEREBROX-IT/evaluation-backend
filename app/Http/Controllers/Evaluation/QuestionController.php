<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class QuestionController extends Controller
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
            return response()->json(['Invalid token or expired'], 200);
        }

        return $user;
    }

    // ================= Create Question =================

    public function createQuestion(Request $request)
    {
        $question = Question::create([
            'evaluation_for' => $request->evaluation_for,
            'evaluation_type' => $request->evaluation_type,
            'question_description' => $request->question_description,
        ]);

        return response()->json(['message' => 'Question created successfully', 'question' => $question], 201);
    }
}
