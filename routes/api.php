<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserAuthController;

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
    Route::post('signin',AuthController::class . '@signin');
    Route::post('signup', AuthController::class . '@signup');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('signout', AuthController::class . '@signout');
        Route::get('user', 'AuthController@user');
    });
});