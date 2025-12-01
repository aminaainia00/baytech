<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('registerAdmin',[UserController::class,'registerAdmin']);
Route::post('register',[UserController::class,'register']);

Route::middleware('auth:sanctum')->group(function()
{
Route::get('getRequests',[UserController::class,'getRequests'])->middleware('CheckAdmin');
Route::get('getRequests/{id}',[UserController::class,'getUser'])->middleware('CheckAdmin');
Route::put('acceptedUser/{id}',[UserController::class,'acceptedUser'])->middleware('CheckAdmin');
Route::delete('rejectionUser/{id}',[UserController::class,'rejectionUser'])->middleware('CheckAdmin');
});
Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth:sanctum');

