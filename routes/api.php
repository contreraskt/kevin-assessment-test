<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Register the user first to create api token*/
Route::post('/register', [AuthController::class, 'register']);

/* Login to get bearer token*/
Route::post('/login',[AuthController::class, 'login']);


/* group middleware for customers api after login*/
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('eligibility/{customer}', 'CustomerController@eligibility');
    Route::post('/photosubmission', 'CustomerController@photosubmission');
});





