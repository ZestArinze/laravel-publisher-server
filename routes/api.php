<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//-------------------------------------------
// protected routes
//-------------------------------------------
Route::group(['middleware' => ['auth:sanctum']], function() {

    // Route::group(['middleware' => ['role:' . Role::ADMIN]], function() {
    // allowing USER role access to all these for easier testing
    Route::group(['middleware' => ['role:' . Role::ADMIN .'.'. Role::USER]], function() {
        Route::get('credentials', [SubscriberController::class, 'makeCredentials']);
        Route::post('topics', [TopicController::class, 'store']);
        Route::delete('topics/{id}', [TopicController::class, 'destroy']);
    });

    Route::group(['middleware' => ['role:' . Role::ADMIN .'.'. Role::USER]], function() {
        Route::post('publish/{topicIdentifier}', [PostController::class, 'store']);
    });

});

//-------------------------------------------
// public routes
//-------------------------------------------
Route::post('register', [AuthController::class, 'createUser']);
Route::post('login', [AuthController::class, 'login']);

Route::get('topics', [TopicController::class, 'index']);
Route::get('topics/{id}', [TopicController::class, 'show']);
Route::post('subscribe/{topicIdentifier}', [TopicController::class, 'subscribe']);

Route::get('posts', [PostController::class, 'index']);