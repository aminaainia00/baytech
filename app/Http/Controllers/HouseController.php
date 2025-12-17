<?php

namespace App\Http\Controllers;

use App\Http\Requests\bookRequest;
use App\Http\Requests\StoreHouseRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateHouseRequest;
use App\Http\Resources\HouseResource;
use App\Models\City;
use App\Models\Governorate;
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
    $user_id = Auth::id();
    $validateData = $request->validated();

    $city = City::where('name', $request->city)->first();
    $governorate = Governorate::where('name', $request->governorate)->first();

    if (!$city ) {
        return response()->json(['errors' => 'The city field is required.'], 422);
    }
     if (!$governorate) {
        return response()->json(['errors' => 'The governorate field is required.'], 422);
    }
    if ($city->governorate_id != $governorate->id) {
        return response()->json([
            'errors' => "The selected city does not belong to the selected governorate."
        ], 422);
    }
    $validateData['user_id'] = $user_id;
    $validateData['city_id'] = $city->id;
    $validateData['governorate_id'] = $governorate->id;

    if ($request->hasFile('mainImage')) {
        $path = $request->file('mainImage')->store('my_photo', 'public');
        $validateData['mainImage'] = $path;
    }

    $house = House::create($validateData);
      $house->load(['city', 'governorate']);
        return new HouseResource($house);

}
    public function getHouses(Request $request){
        $user_id=Auth::user()->id;
       echo $request->query('title_search');
        $houses = House::query()
        ->when($search=$request->query('search'), function($query, $search) {
                $query->where('descreption','LIKE' ,"%{$search}%");
            })
        ->when($governorate_search = $request->query('governorate_search'), function ($query, $governorate_search) {
            $names = is_string($governorate_search) ? explode(',', $governorate_search) : $governorate_search;

            $ids = Governorate::whereIn('name', $names)->pluck('id');

            $query->whereIn('governorate_id', $ids);
        })
        ->when($city_search = $request->query('city_search'), function ($query, $city_search) {
            $names = is_string($city_search) ? explode(',', $city_search) : $city_search;

            $ids = City::whereIn('name', $names)->pluck('id');

            $query->whereIn('city_id', $ids);
        })

            ->when($title_search=$request->query('title_search'), function($query, $title_search) {
                $query->where('title','LIKE' ,"%{$title_search}%");
            })
            ->when($request->query('category_search'), function($query,$category_search ) {
                if (is_string($category_search)) {
                    $category_search = explode(',', $category_search);
                }
                $query->whereIn('category', $category_search);
            })
            ->when($request->query('min_price'), function($query, $minPrice) {
                $query->whereRaw('CAST(day_price AS UNSIGNED) >= ?', [(double) $minPrice]);
            })
            ->when($request->query('max_price'), function($query, $maxPrice) {
                $query->whereRaw('CAST(day_price AS UNSIGNED) <= ?', [(double) $maxPrice]);
            })
            ->when($request->query('min_area'), function($query, $minArea) {
                $query->whereRaw('CAST(area AS UNSIGNED) >= ?', [(double) $minArea]);
            })
            ->when($request->query('max_area'), function($query, $maxArea) {
                $query->whereRaw('CAST(area AS UNSIGNED) <= ?', [(double)$maxArea]);
            })
            ->when($request->query('min_bedrooms'), function($query, $minBedrooms) {
                $query->whereRaw('CAST(bedrooms AS UNSIGNED) >= ?',[(int) $minBedrooms]);
            })
            ->when($request->query('max_bedrooms'), function($query, $maxBedrooms) {
                $query->whereRaw('CAST(bedrooms AS UNSIGNED) <= ?',[(int) $maxBedrooms]);
            })
            ->when($request->query('min_bathrooms'), function($query, $minBathrooms) {
                $query->whereRaw('CAST(bathrooms AS UNSIGNED) >= ?', [(int) $minBathrooms]);
            })
            ->when($request->query('max_bathrooms'), function($query, $maxBathrooms) {
                $query->whereRaw('CAST(bathrooms AS UNSIGNED) <= ?', [(int)$maxBathrooms]);
            })
            ->when($request->query('min_livingrooms'), function($query, $minLivingrooms) {
                $query->whereRaw('CAST(livingrooms AS UNSIGNED) >= ?',[(int)$minLivingrooms]);
            })
            ->when($request->query('max_livingrooms'), function($query, $maxLivingrooms) {
                $query->whereRaw('CAST(livingrooms AS UNSIGNED) <= ?', [(int) $maxLivingrooms]);
            })->where('user_id','!=',$user_id)
            ->with(['city', 'governorate'])->withAvg('evaluations','star')->get();

          return  HouseResource::collection($houses);
}

public function getHousesForOwner()
    {
            $user_id = Auth::id();
            $houses=House::where('user_id',$user_id)->with(['city', 'governorate'])->withAvg('evaluations','star')->get();
             if (count($houses)!==0)
             return  HouseResource::collection( $houses);
             return response()->json([
              'errors'=>'you don\'t have any house'],200);
    }


    //  public function getDetailsHouses($houseId){
    //     try{
    //         $houseData=House::findOrFail($houseId);
    //        return response()->json(['House'=>$houseData],200);
    //        }
    //     catch(ModelNotFoundException $ex){
    //         return response()->json([
    //           'errors'=>'this house not found',
    //           'details'=>$ex->getMessage()],404);}
    // }

   public function updateHouse(UpdateHouseRequest $request, $id)
{
    try {
        $user_id = Auth::id();
        $house = House::findOrFail($id);

        if ($house->user_id != $user_id) {
            return response()->json(['message' => 'unauthorized'], 403);
        }

        $houseData = $request->all();
        $governorateChanged = false;
        $governorate = null;
        if ($request->has('governorate')) {
            $governorateName = $request->governorate;

           $governorate = Governorate::where('name', $governorateName)->first();
            if (!$governorate) {
                return response()->json([
                    'message' => "Governorate '{$governorateName}' not found"
                ], 422);
            }
             if ($house->governorate_id != $governorate->id) {
                $governorateChanged = true;
            }
            $houseData['governorate_id'] = $governorate->id;
        }

        if ($request->has('city')) {
            $cityName = $request->city;

            $city = City::where('name', $cityName)->first();
            if (!$city) {
                return response()->json([
                    'message' => "City '{$cityName}' not found"
                ], 422);
            }
              if ($governorateChanged && $city->governorate_id != $governorate->id) {
                return response()->json([
                    'errors' => "The selected city does not belong to the selected governorate."
                ], 422);
            }
            else if (!$governorateChanged && $city->governorate_id != $house->governorate_id){
                return response()->json([
                    'errors' => "The selected city does not belong to the governorate."
                ], 422);
            }
           $houseData['city_id'] = $city->id;
        }
        elseif ($governorateChanged) {
            return response()->json([
                'errors' => "You must select a city when changing the governorate."
            ], 422);

        }
        unset($houseData['mainImage'], $houseData['governorate'], $houseData['city']);

        $house->update($houseData);
        if ($request->hasFile('mainImage')) {
            $path = $request->file('mainImage')->store('my_photo', 'public');
            $house->mainImage = $path;
            $house->save();
        }
        $house->load(['city', 'governorate']);
        return new HouseResource($house);

    } catch (ModelNotFoundException $ex) {
        return response()->json([
            'errors'  => 'this house not found',
            'details' => $ex->getMessage()
        ], 404);
    }

 }

    public function destroyHouse($id)
    {
    try{
    $user_id = Auth::id();
    $house = House::findOrFail($id);

    if ($house->user_id != $user_id) {
        return response()->json(['message' => 'unauthorized'], 403);
    }
     $house->delete();
     return  response()->json(['message'=>'the house was deleted'],200);}
     catch(ModelNotFoundException $ex){
       return response()->json([
              'errors'=>'this house not found',
              'details'=>$ex->getMessage()],404);
        }
    }

public function addToFavorites($houseId)
    {
         try{
        House::findOrFail($houseId);
        Auth::user()->favoriteHouses()->syncWithoutDetaching($houseId);
       return response()->json(['message'=>'House added to favorites'], 200);}
       catch(ModelNotFoundException $ex){
       return response()->json([
              'errors'=>'this house not found',
              'details'=>$ex->getMessage()],404);

            }
    }
public function removeFromFavorites($houseId)
    {
        try{
        House::findOrFail($houseId);
        Auth::user()->favoriteHouses()->detach($houseId);
        return response()->json(['message'=>'House removed from favorites'], 200);}
        catch(ModelNotFoundException $ex){
        return response()->json([
              'errors'=>'this house not found',
              'details'=>$ex->getMessage()],404);

            }
    }

    public function getFavoriteHousesByUser(){

        $houses=Auth::user()->favoriteHouses()->with(['city', 'governorate'])
        ->withAvg('evaluations','star')->get();
        if (count($houses)!==0)
             return  HouseResource::collection($houses);
             return response()->json([
              'errors'=>'you don\'t have any favorite house'],200);
    }
      public function isFavoriteHouseByUser($houseId){

        $houses_favorites=Auth::user()->favoriteHouses;
        foreach ($houses_favorites as $house_favorite)
        {
            $house_id=$house_favorite->id;
            if ($houseId==$house_id)
            return 1;
        }
            return 0;
    }
    // public function addToBooks(bookRequest $request,$houseId)
    // {
    //      $house=House::findOrFail($houseId);
    //  $house->bookHouses()->attach(Auth::id());

    //  return response()->json('category attached successfully', 200);
    // }

}
