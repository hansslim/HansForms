<?php

use App\Http\Controllers\FormCompletionController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\UserController;
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
Route::get('/private_form/{token}', [FormController::class, 'privateShow']);

Route::post('/form/complete/{slug}', [FormCompletionController::class, 'store']);
Route::post('/form/private_complete/{token}', [FormCompletionController::class, 'privateStore']);

Route::get('/form/public_results/{slug}', [FormCompletionController::class, 'publicIndex']);

//require user privileges
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/form/authenticated/{slug}', [FormController::class, 'showWithAuth']);
    Route::get('/forms', [FormController::class, 'index']);
    Route::put('/forms/update/{slug}', [FormController::class, 'update']);
    Route::post('/forms/create', [FormController::class, 'store']);
    Route::delete('/forms/{id}', [FormController::class, 'destroy']); //todo: check {id}
    Route::post('/form/duplicate', [FormController::class, 'duplicateWithAuth']);
    Route::get('/form/results/{slug}', [FormCompletionController::class, 'index']);
    Route::get('/form/results/{slug}/download', [FormCompletionController::class, 'export']);
    Route::post('/form/results/{slug}/publish_results', [FormController::class, 'publishResults']);
    Route::put('/forms/update_access/{slug}', [FormController::class, 'updateAccess']);

    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/change_password', [UserController::class, 'changePassword']);
    Route::post('/delete_account', [UserController::class, 'destroy']);
});
