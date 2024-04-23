<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('screen/authentication/login');
    });
    // Login
    Route::post('/login', [AuthController::class, 'adminLogin'])->name('user-login');
    // Reset Password
    Route::get('/reset-password', function () {
        // Retrieve the token from the query parameters
        $token = request()->query('token');
        // You can add your logic here to handle the token, such as verifying it or rendering a view
        return view('screen/authentication/reset')->with('token', $token);
    })->name('reset-password');
    Route::post('/set-new-password', [AuthController::class, 'setNewPassword'])->name('set-new-password');
});

Route::get('/dashboard', function () {
    return view('screen/dashboard/index');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
