<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHouseRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateHouseRequest;
use App\Http\Resources\HouseResource;
use App\Models\House;
use App\Models\Image;
use App\Models\User;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Termwind\Components\Hr;

class HouseController extends Controller
{
    public function storeHouse(StoreHouseRequest $request)
    {
        $user_id=Auth::user()->id;
        $validateData=$request->validated();
        $validateData['user_id']= $user_id;
        if($request->hasFile('mainImage'))
               {
                 $path= $request->file('mainImage')->store('my photo','public');
                 $validateData['mainImage']=$path;
               }
        $house=House::create( $validateData);
        return response()->json(  $house, 201);

    }
    public function getHouses(){
        $houses=House::select('id','title','mainImage','country','city')->get();

        return  HouseResource::collection($houses);
    }
     public function getDetailsHouses($houseId){
        try{
            $houseData=House::findOrFail($houseId);
           return response()->json($houseData,200);
           }
        catch(ModelNotFoundException $ex){
            return response()->json([
              'message'=>'this house not found',
              'details'=>$ex->getMessage()],404);}

    }

    public function updateHouse(UpdateHouseRequest $request, $id)
{
    try{
    $user_id = Auth::id();
    $house = House::findOrFail($id);

    if ($house->user_id != $user_id) {
        return response()->json(['message' => 'unauthorized'], 403);
    }

    $house->update($request->except('mainImage'));


    if ($request->hasFile('mainImage')) {
        $path = $request->file('mainImage')->store('my photo', 'public');
        $house->mainImage = $path;
        $house->save();
    }
     return response()->json($house, 200);
}
catch(ModelNotFoundException $ex){
       return response()->json([
              'message'=>'this house not found',
              'details'=>$ex->getMessage()],404);
     }
}

    public function destroyHouse($id)
    {
        try{
     $house=House::findOrFail($id);
     $house->delete();
     return  response()->json(['message'=>'the house was deleted'],200);}
     catch(ModelNotFoundException $ex){
       return response()->json([
              'message'=>'this house not found',
              'details'=>$ex->getMessage()],404);
        }
    }
    


}
