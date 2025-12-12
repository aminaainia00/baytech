<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\House;
use App\Models\Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function storeImages(StoreImageRequest $request, $id)
    {
    try {
        $house = House::findOrFail($id);
        if ($request->hasFile('houseImages')) {
            foreach ($request->file('houseImages') as $image) {
                $path = $image->store('my_photo', 'public');
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
            'errors' => 'this house not found',
            'details' => $ex->getMessage()
        ], 404);
    }
}
 public function destroyImage($id)
    {
    try{

     $user_id = Auth::id();
     $image=Image::findOrFail($id);
     $house_id=$image->house_id;
     $userId=House::select('user_id')->where('id',$house_id)->first();

    if ($userId->user_id != $user_id) {
         return response()->json(['message' => 'unauthorized'], 403);
     }
     $imageData= Image::where('house_id',$house_id)->get();
     if (count($imageData)<=1){
        return  response()->json(['message'=>'sorry,you can\'t delete this photo, you must have one photo at least'],200);
     }
     $image->delete();
     return  response()->json(['message'=>'the image was deleted'],200);}
     catch(ModelNotFoundException $ex){
       return response()->json([
              'errors'=>'this image not found',
              'details'=>$ex->getMessage()],404);
        }
    }
   public function getImages($Id){

           $imageData= Image::where('house_id',$Id)->get();
           if (count($imageData)!==0)
           return  ImageResource::collection($imageData);
           return response()->json([
              'errors'=>'imeges not found'],404);

    }


}
