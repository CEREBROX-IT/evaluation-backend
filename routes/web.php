<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('screen/authentication/login');
});

Route::get('/reset-password', function () {
    // Retrieve the token from the query parameters
    $token = request()->query('token');

    // You can add your logic here to handle the token, such as verifying it or rendering a view
    return view('screen/authentication/reset')->with('token', $token);
})->name('reset-password');

Route::post('/set-new-password', [AuthController::class, 'setNewPassword'])->name('set-new-password');
