<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use \App\Http\Controllers\RoleController;
use \App\Http\Controllers\ProjectController;
use \App\Http\Controllers\AccessController;
use \App\Http\Controllers\SurveyController;
use \App\Http\Controllers\QuestionController;
use \App\Http\Controllers\SectionController;
use \App\Http\Controllers\ImageController;
use \App\Http\Controllers\OptionController;
use \App\Http\Controllers\AnswerController;
use \App\Http\Controllers\MenuController;
use \App\Http\Controllers\DpaController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::prefix('v1')->group(function () {
    //Prefix auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        //routes that require authentications
        Route::middleware('auth:api')->group(function () {
            Route::get('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::resource('/users',UserController::class);
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/me/navigation', [MenuController::class, 'navigation']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::resource('/roles',RoleController::class);
        Route::post('/roles/{role}/access', [RoleController::class, 'setAccess']);
        Route::delete('/roles/{role}/access', [RoleController::class, 'removeAccess']);
        Route::get('/access', [AccessController::class, 'index']);
        Route::resource('/projects',ProjectController::class);
        Route::post('/projects/{project}/members',[ProjectController::class,'addUsers']);
        Route::get('/projects/{project}/members',[ProjectController::class,'getUsers']);
        Route::delete('/projects/{project}/members',[ProjectController::class,'removeUsers']);
        Route::get('/projects/{project}/surveys',[SurveyController::class,'index']);
        Route::post('/projects/{project}/surveys',[SurveyController::class,'store']);
        Route::put('/surveys/{survey}',[SurveyController::class,'update']);
        Route::delete('/surveys/{survey}',[SurveyController::class,'destroy']);
        Route::get('/surveys/{survey}',[SurveyController::class,'show']);
        Route::get('/surveys/{survey}/questions',[QuestionController::class,'index']);
        Route::post('/surveys/{survey}/sections',[SectionController::class,'store']);
        Route::put('/sections/{section}',[SectionController::class,'update']);
        Route::delete('/sections/{section}',[SectionController::class,'destroy']);
        Route::post('/sections/{section}/questions',[QuestionController::class,'store']);
        Route::put('/questions/{question}',[QuestionController::class,'update']);
        Route::delete('/questions/{question}',[QuestionController::class,'destroy']);
        Route::post('/questions/{question}/options',[OptionController::class,'store']);
        Route::put('/options/{option}',[OptionController::class,'update']);
        Route::delete('/options/{option}',[OptionController::class,'destroy']);
        Route::post('/answers',[AnswerController::class,'store']);
        Route::delete('/answers/{answer}',[AnswerController::class,'destroy']);
        Route::post('/image',[ImageController::class,'uploadImage']);
        Route::get('/image/{img}',[ImageController::class,'viewImage']);
        Route::resource('/dpa',DpaController::class);
    });
});

