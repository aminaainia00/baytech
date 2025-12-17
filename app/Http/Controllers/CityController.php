<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function getCitesForGovernorate(Request $request){
        $governorate_id=Governorate::where('name',$request->governorate)->pluck('id')->first();
        $cities = City::where('governorate_id', $governorate_id)->get();
        $city_data=[];
        foreach($cities as $city)
        {
            $city_data[]=[
                'city'=> $city->name,];}
        return response()->json($city_data);
    }
}
