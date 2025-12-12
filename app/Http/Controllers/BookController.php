<?php

namespace App\Http\Controllers;

use App\Http\Requests\bookRequest;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // public function storeBookHouse(bookRequest $request,$houseId)
    // {
    //      $user_id=Auth::user()->id;

    //     $validateData=$request->validated();
    //     $validateData['user_id']= $user_id;
    //     $validateData['house_id']=$houseId;
    //     $bookHouses=House::create($validateData);



    // }

}
