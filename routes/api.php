<?php

use App\Http\Controllers\HouseController;
use App\Http\Controllers\ImageController;
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
Route::get('getUserForActive',[UserController::class,'getUserForActive']);

Route::middleware('CheckActive')->group(function(){
Route::post('storeHouse',[HouseController::class,'storeHouse']);
Route::post('storeImages/{id}',[ImageController::class,'storeImages']);
Route::delete('destroyImage/{id}',[ImageController::class,'destroyImage']);
Route::get('getHouses',[HouseController::class,'getHouses']);
Route::put('requestDelete',[UserController::class,'requestDelete']);
Route::put('updateHouse/{id}',[HouseController::class,'updateHouse']);
Route::delete('destroyHouse/{id}',[HouseController::class,'destroyHouse']);
Route::get('getDetailsHouses/{id}',[HouseController::class,'getDetailsHouses']);});


Route::middleware('CheckAdmin')->group(function(){
Route::get('getRegisterRequests',[UserController::class,'getRegisterRequests']);
Route::get('getUser/{id}',[UserController::class,'getUser']);
Route::put('acceptedUser/{id}',[UserController::class,'acceptedUser']);
Route::delete('rejectionUser/{id}',[UserController::class,'rejectionUser']);
Route::get('getDeleteRequests',[UserController::class,'getDeleteRequests']);
Route::delete('acceptedDeleteUser/{id}',[UserController::class,'acceptedDeleteUser']);
Route::put('rejectionDeleteUser/{id}',[UserController::class,'rejectionDeleteUser']);
Route::put('addToAccount',[UserController::class,'addToAccount']);});
});

Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth:sanctum');

