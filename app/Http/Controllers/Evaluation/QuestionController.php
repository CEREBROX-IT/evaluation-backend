<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        if (!$user) {
            return response()->json(['error' => 'Invalid token or expired'], 401);
        }
        return $user;
    }

    // ================= Get Questions with Status True =================
    public function getQuestions(Request $request, $type)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        $questions = Question::where('status', true)
            ->where('type', $type)
            ->orderBy('updated_at', 'desc')
            ->get();
        return response()->json(['questions' => $questions], 201);
    }

    // ================= Function to get all question =================
    public function getAllQuestions(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        $questions = Question::where('status', true)->orderBy('updated_at', 'desc')->get();
        return response()->json(['questions' => $questions], 200);
    }

    // ================= Create Question =================

    public function createQuestion(Request $request)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        $question = Question::create([
            'type' => $request->type,
            'question_group' => $request->question_group,
            'evaluation_type' => $request->evaluation_type,
            'question_description' => $request->question_description,
            'status' => true,
        ]);
        return response()->json(['message' => 'Question created successfully', 'question' => $question], 201);
    }

    // ================= Update Question =================
    public function updateQuestion(Request $request, $id)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }
        $question->update([
            'type' => $request->type,
            'question_group' => $request->question_group,
            'evaluation_type' => $request->evaluation_type,
            'question_description' => $request->question_description,
        ]);

        return response()->json(['message' => 'Question updated successfully', 'question' => $question], 201);
    }

    // ================= Delete Question =================
    public function deleteQuestion(Request $request, $id)
    {
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user;
        }
        $question = Question::find($id);
        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }
        $question->update(['status' => false]);
        return response()->json(['message' => 'Question deleted successfully'], 201);
    }
}
