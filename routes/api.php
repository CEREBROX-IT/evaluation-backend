<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Evaluation\QuestionController;

// for API testing if it connected
Route::get('/testing', function () {
    return 'REST API is connected Successfully';
});

// ============== Evaluation Question Endpoints ==============

Route::get('/question/get/{status}', [QuestionController::class, 'getQuestions']);

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
Route::get('/get-user/{role}', [AuthController::class, 'getUsersRole']);

// Logout (protected route, requires authentication)
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
