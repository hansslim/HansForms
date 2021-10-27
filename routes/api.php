<?php

use App\Http\Controllers\FormsController;
use App\Http\Controllers\UserController;
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

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/logged_user', [UserController::class, 'show']);

Route::get('/form/{slug}', [FormsController::class, 'show']);

//require user privileges
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/forms', [FormsController::class, 'index']);
    Route::put('/forms/{id}', [FormsController::class, 'update']);
    Route::post('/forms', [FormsController::class, 'store']);
    Route::delete('/forms/{id}', [FormsController::class, 'destroy']);

    Route::post('/logout', [UserController::class, 'logout']);
});

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
