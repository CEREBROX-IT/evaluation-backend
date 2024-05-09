<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Authentication\SessionController;
use App\Http\Controllers\Evaluation\QuestionController;
use App\Http\Controllers\Evaluation\ResultController;
use App\Http\Controllers\Evaluation\EvaluationController;

Route::get('/', function () {
    return view('screen/test');
});
// ============== Statistic ==============
Route::get('total/user-count', [AuthController::class, 'userTotal']);
Route::get('total/users-evaluated', [EvaluationController::class, 'getUserEvaluated']);
// ============== Create Session School Year ==============
Route::post('school-year/create', [SessionController::class, 'createSessionSchoolYear']);
Route::get('school-year/list', [SessionController::class, 'getSessionList']);

// ============== Evaluation and Evaluation Result Endpoints ==============
Route::get('/users/not-evaluated/status={status}', [EvaluationController::class, 'getUsersNotEvaluated']);
Route::post('evaluation-result/create', [ResultController::class, 'createEvaluationResult']);
Route::get('/rating-total', [ResultController::class, 'getRatingTotal']);
Route::get('question-description/rating-total/', [ResultController::class, 'getQuestionRating']);
Route::get('evaluation-form/comment-suggestion', [EvaluationController::class, 'getComments']);
Route::get('list-approved/userid={userid}', [ResultController::class, 'getApproveComments']);
Route::post('evaluation-form/update/{id}', [EvaluationController::class, 'updateEvaluation']);
Route::post('evaluation-form/approve/{id}', [EvaluationController::class, 'approveEvaluation']);
Route::get('/list-evaluated-category/userId={userId}', [ResultController::class, 'checkEvaluation']);

// ============== Evaluation Question Endpoints ==============
Route::get('/question/get/type={type}', [QuestionController::class, 'getQuestions']);
Route::post('/question/create', [QuestionController::class, 'createQuestion']);
Route::post('/question/update/{id}', [QuestionController::class, 'updateQuestion']);
Route::delete('/question/delete/{id}', [QuestionController::class, 'deleteQuestion']);

// ============== Authentication Endpoints ==============
Route::get('/email/verify/{id}/{hash}', 'App\Http\Controllers\Authentication\VerificationController@verify')->name('verification.verify');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user/profile/userId={userId}', [AuthController::class, 'getUserProfile']);
Route::post('/user/updateprofile/{id}', [AuthController::class, 'updateProfile']);
Route::post('/super-admin/update-details/userid={id}', [AuthController::class, 'updateUserDetails']);
Route::post('/user/update-password/{id}', [AuthController::class, 'updatePassword']);
Route::post('/users/update-email/{id}', [AuthController::class, 'updateEmail']);
Route::post('/user/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/get-user/role={role}', [AuthController::class, 'getUsersRole']);
Route::get('/get-user', [AuthController::class, 'getUserList']);
Route::delete('/delete-user/id={id}', [AuthController::class, 'deleteUser']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============== Testing Endpoints ==============
Route::get('/temporary/evaluation-result/resultid={id}', [ResultController::class, 'getEvaluationResult']);
Route::get('/temporary/evaluation-form/resultid={id}', [EvaluationController::class, 'getEvaluation']);
