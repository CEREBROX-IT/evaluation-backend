<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;

Route::get('/', [AuthController::class, 'index'])->name('welcome');

// Reset Password
Route::get('/reset-password', function () {
    // Retrieve the token from the query parameters
    $token = request()->query('token');
    // You can add your logic here to handle the token, such as verifying it or rendering a view
    return view('screen/authentication/reset')->with('token', $token);
})->name('reset-password');
Route::post('/set-new-password', [AuthController::class, 'setNewPassword'])->name('set-new-password');
