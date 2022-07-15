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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('signin',AuthController::class . '@signin'); //Login    
    Route::post('signup', AuthController::class . '@signup'); //Register

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('signout', AuthController::class . '@signout'); // Logout
        Route::get('user', AuthController::class . '@user'); // get user info 
        Route::put('user', AuthController::class . '@update'); // update user profile
        Route::delete('user', AuthController::class . '@destroy'); // delete user
    });
});