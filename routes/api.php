<?php

use App\Http\Controllers\FormCompletionController;
use App\Http\Controllers\FormController;
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

Route::get('/form/{slug}', [FormController::class, 'show']);
Route::post('/form/complete/{slug}', [FormCompletionController::class, 'store']);

//require user privileges
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/form/authenticated/{slug}', [FormController::class, 'showWithAuth']);
    Route::get('/forms', [FormController::class, 'index']);
    Route::put('/forms/{id}', [FormController::class, 'update']);
    Route::post('/forms', [FormController::class, 'store']);
    Route::delete('/forms/{id}', [FormController::class, 'destroy']);
    Route::post('/form/duplicate/', [FormController::class, 'duplicateWithAuth']);
    Route::get('/form/results/{slug}', [FormCompletionController::class, 'index']);

    Route::post('/logout', [UserController::class, 'logout']);
});

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
