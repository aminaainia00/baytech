<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\EvaluationController;
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
Route::get('getImages/{id}',[ImageController::class,'getImages']);
Route::get('getHouses',[HouseController::class,'getHouses']);
Route::get('getHousesForOwner',[HouseController::class,'getHousesForOwner']);
Route::put('requestDelete',[UserController::class,'requestDelete']);
Route::put('updateHouse/{id}',[HouseController::class,'updateHouse']);
Route::delete('destroyHouse/{id}',[HouseController::class,'destroyHouse']);
Route::post('house/{id}/favorite',[HouseController::class,'addToFavorites']);
Route::delete('house/{id}/favorite',[HouseController::class,'removeFromFavorites']);
Route::get('getFavoriteHousesByUser',[HouseController::class,'getFavoriteHousesByUser']);
Route::get('isFavoriteHouseByUser/{id}',[HouseController::class,'isFavoriteHouseByUser']);
Route::get('getCitesForGovernorate',[CityController::class,'getCitesForGovernorate']);
Route::post('storeBook/{id}',[BookController::class,'storeBook']);
Route::get('BookingRequests/{id}',[BookController::class,'BookingRequests']);
Route::put('acceptedeBooking/{id}',[BookController::class,'acceptedeBooking']);
Route::put('RejectionBooking/{id}',[BookController::class,'RejectionBooking']);
Route::put('cancelBooking/{id}',[BookController::class,'cancelBooking']);
Route::get('getMyBookings',[BookController::class,'getMyBookings']);
Route::put('updateBooking/{id}',[BookController::class,'updateBooking']);
Route::get('updateRequests/{id}',[BookController::class,'updateRequests']);
Route::put('acceptedUpdateBookRequest/{id}',[BookController::class,'acceptedUpdateBookRequest']);
Route::put('RejectionUpdateBookRequest/{id}',[BookController::class,'RejectionUpdateBookRequest']);
Route::get('getBookForHouse/{id}',[BookController::class,'getBookForHouse']);

Route::post('storeEvaluation/{id}',[EvaluationController::class,'storeEvaluation']);
Route::get('getHousesByEvaluation',[EvaluationController::class,'getHousesByEvaluation']);

Route::get('getNotificationsForUser',[UserController::class,'getNotificationsForUser']);
//Route::get('getDetailsHouses/{id}',[HouseController::class,'getDetailsHouses']);
});

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

