<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\HouseResource;
use App\Models\Book;
use App\Models\Evaluation;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function storeEvaluation(EvaluationRequest $request,$houseId){
        $userId=Auth::id();
        $exists = Evaluation::where('user_id',$userId )
        ->where('house_id', $houseId)
        ->exists();
        if ($exists) {
         return response()->json([
        'errors' => 'You have already evaluate this house'
         ], 422);
        }
$hasFinishedBooking = Book::where('user_id',$userId)
    ->where('house_id', $houseId)
    ->where('end_date', '<', now())
    ->where('book_status', 'accepted')
    ->exists();

if (!$hasFinishedBooking) {
    return response()->json([
        'errors' => 'You can only evaluate after finishing your booking'
    ], 422);
}
                     $validatedData=$request->validated();
                     $validatedData['user_id']=$userId;
                     $validatedData['house_id']=$houseId;
                     $evaluation=Evaluation::create($validatedData);
                     return new EvaluationResource($evaluation);
 }


    public function getHousesByEvaluation(Request $request){


    $order = $request->get('order', 'desc'); // asc | desc

    $houses = House::withAvg('evaluations', 'star')
        ->orderBy('evaluations_avg_star', $order)
        ->get();

    return HouseResource::collection($houses);

    }
}
