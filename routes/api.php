<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Authentication\SessionController;
use App\Http\Controllers\Evaluation\QuestionController;
use App\Http\Controllers\Evaluation\ResultController;
use App\Http\Controllers\Evaluation\EvaluationController;
// for API testing if it connected
Route::get('/testing', function () {
    return 'REST API is connected Successfully';
});
// ============== Statistic ==============
Route::get('total/students', [AuthController::class, 'studentTotal']);
Route::get('total/teachers', [AuthController::class, 'teacherTotal']);
Route::get('total/teachers-evaluated', [EvaluationController::class, 'getTeacherEvaluated']);
Route::get('total/students-evaluated', [EvaluationController::class, 'getStudentEvaluated']);
// ============== Create Session School Year ==============

// Create Session school year (Tempoary)
Route::post('school-year/create', [SessionController::class, 'createSessionSchoolYear']);
Route::get('school-year/list', [SessionController::class, 'getSessionList']);
// ============== Evaluation and Evaluation Result Endpoints ==============
// Create Evaluation result
Route::get('/users/not-evaluated/status={status}', [EvaluationController::class, 'getUsersNotEvaluated']);
Route::post('evaluation-result/create', [ResultController::class, 'createEvaluationResult']);
Route::get('/rating-total', [ResultController::class, 'getRatingTotal']);
Route::get('question-description/rating-total/', [ResultController::class, 'getQuestionRating']);

// Update Comments and Suggestion
Route::get('evaluation-form/comment-suggestion/status={status}', [EvaluationController::class, 'getComments']);

Route::post('evaluation-form/update/{id}', [EvaluationController::class, 'updateEvaluation']);

Route::post('evaluation-form/approve/{id}', [EvaluationController::class, 'approveEvaluation']);

// ============== Evaluation Question Endpoints ==============
Route::get('/question/get/status={status}', [QuestionController::class, 'getQuestions']);

//create qeustion
Route::post('/question/create', [QuestionController::class, 'createQuestion']);

//update question
Route::post('/question/update/{id}', [QuestionController::class, 'updateQuestion']);

// Delete question route
Route::delete('/question/delete/{id}', [QuestionController::class, 'deleteQuestion']);

// ============== Authentication Endpoints ==============
Route::get('/email/verify/{id}/{hash}', 'App\Http\Controllers\Authentication\VerificationController@verify')->name('verification.verify');

// Registration
Route::post('/register', [AuthController::class, 'register']);

// Login
Route::post('/login', [AuthController::class, 'login']);

// Update Profile (no authentication required)
Route::post('/user/updateprofile/{id}', [AuthController::class, 'updateProfile']);

// Update Password (without authentication)
Route::post('/user/update-password/{id}', [AuthController::class, 'updatePassword']);

// Update User Email Address
Route::post('/users/update-email/{id}', [AuthController::class, 'updateEmail']);

// Forgot Password
Route::post('/user/reset-password', [AuthController::class, 'resetPassword']);

// To get User Base on the Role
Route::get('/get-user/role={role}', [AuthController::class, 'getUsersRole']);

// To get list of user where status true
Route::get('/get-user', [AuthController::class, 'getUserList']);

// To delete User
Route::delete('/delete-user/id={id}', [AuthController::class, 'deleteUser']);

// Logout (protected route, requires authentication)
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
