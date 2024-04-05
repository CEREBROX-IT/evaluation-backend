<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;

// for API testing if it connected
Route::get('/testing', function () {
    return 'REST API is connected Successfully';
});

// Registration
Route::post('/register', [AuthController::class, 'register']);

// Login
Route::post('/login', [AuthController::class, 'login']);

// Update Profile (no authentication required)
Route::post('/updateprofile/{id}', [AuthController::class, 'updateProfile']);

// Logout (protected route, requires authentication)
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
