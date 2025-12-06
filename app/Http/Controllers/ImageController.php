<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Models\House;
use App\Models\Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    //
     public function storeImages(StoreImageRequest $request, $id)
{
    try {

        $house = House::findOrFail($id);

        if ($request->hasFile('houseImages')) {

            foreach ($request->file('houseImages') as $image) {

                $path = $image->store('my_photos', 'public');

                Image::create([
                    'house_id' => $house->id,
                    'houseImages' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Images added successfully'
        ], 201);

    } catch (ModelNotFoundException $ex) {

        return response()->json([
            'message' => 'this house not found',
            'details' => $ex->getMessage()
        ], 404);
    }
}
 public function destroyImage($id)
    {
        try{
     $image=Image::findOrFail($id);
     $image->delete();
     return  response()->json(['message'=>'the image was deleted'],200);}
     catch(ModelNotFoundException $ex){
       return response()->json([
              'message'=>'this image not found',
              'details'=>$ex->getMessage()],404);
        }
    }

}
