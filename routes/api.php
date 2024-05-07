<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/get_task',[TaskController::class,'getTask']); //logged In user tasks
    Route::get('/get_all_task',[TaskController::class,'getAllTask']); //All tasks
    Route::post('/get_task',[TaskController::class,'getFilteredTask']); // filter task according to parameter

    Route::post('/add_update_task',[TaskController::class,'addUpdateTask']);
    Route::post('/update_status',[TaskController::class,'updateStatus']);
    Route::delete('/delete_task/{id}',[TaskController::class,'deleteTask']);
    Route::post('/add_user_to_task',[TaskController::class,'addUserToTask']);
    Route::post('/remove_user_from_task',[TaskController::class,'removeUserFromTask']);

});

