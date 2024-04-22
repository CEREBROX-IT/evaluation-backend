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

        // Check if $user is null, indicating invalid or expired token
        if (!$user) {
            return response()->json(['error' => 'Invalid token or expired'], 401);
        }

        return $user;
    }

    // ================= Get Questions with Status True =================
    public function getQuestions(Request $request, $status)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Fetch questions with status true
        $questions = Question::where('status', $status)->get();

        return response()->json(['questions' => $questions], 201);
    }

    // ================= Create Question =================

    public function createQuestion(Request $request)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if the authenticated user is an admin
        if ($user->role !== 'Admin' || $user->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        $question = Question::create([
            'evaluation_for' => $request->evaluation_for,
            'evaluation_type' => $request->evaluation_type,
            'question_description' => $request->question_description,
            'status' => true,
        ]);

        return response()->json(['message' => 'Question created successfully', 'question' => $question], 201);
    }

    // ================= Update Question =================
    public function updateQuestion(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if the authenticated user is an admin
        if ($user->role !== 'Admin' || $user->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        $question = Question::find($id);

        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        //update the question
        $question->update([
            'evaluation_for' => $request->evaluation_for,
            'evaluation_type' => $request->evaluation_type,
            'question_description' => $request->question_description,
        ]);

        return response()->json(['message' => 'Question updated successfully', 'question' => $question], 201);
    }

    // ================= Delete Question =================
    public function deleteQuestion(Request $request, $id)
    {
        // Check if the request has valid authorization token
        $user = $this->authorizeRequest($request);
        if (!$user instanceof User) {
            return $user; // Return the response if authorization fails
        }

        // Check if the authenticated user is an admin
        if ($user->role !== 'Admin' || $user->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized Request'], 401);
        }

        // Find the question
        $question = Question::find($id);

        // Check if the question exists
        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        // Update the status of the question to false
        $question->update(['status' => false]);

        // Return success response
        return response()->json(['message' => 'Question deleted successfully'], 201);
    }
}
