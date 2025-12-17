<?php

namespace App\Http\Controllers;

use App\Http\Requests\bookRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\UpdateBookeResourse;
use App\Models\Book;
use App\Models\House;
use App\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function storeBook(bookRequest $request,$id){
    $user_id = Auth::id();
    $house = House::findOrFail($id);
    $user = User::findOrFail($user_id);
    $overlap = Book::where('house_id', $id)
    ->where('book_status','accepted')
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                          ->where('end_date', '>=', $request->end_date);
                  });
        })
        ->exists();

    if ($overlap) {
        return response()->json([
            "erorrs" => "This house is already booked for that period."
        ], 422);
    }
    $start=new DateTime($request->start_date);
    $end=new DateTime($request->end_date);
    $total_days=$end->diff($start);
    $total_price=($total_days->days+1)*($house->day_price);

     if ($total_price>$user->account){
        return response()->json([
            "errors" => "The money in your account is not enough for the booking price."
        ], 422);
     }
    $booking = Book::create([
        'user_id' => $user_id,
        'house_id' => $id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_price'=>$total_price
    ]);

    return new BookResource($booking);
}
public function BookingRequests($houseId)
{
    $BookingRequests= Book::where('house_id', $houseId)->where('book_status','pending')->orderBy('start_date', 'desc')->get();
    $user_id=Auth::id();
    $userId=House::select('user_id')->where('id',$houseId)->first();
    if ($user_id!==$userId->user_id)
         return response()->json(['errors' => 'unauthorized'], 403);
    if (count($BookingRequests)!==0)
             return  BookResource::collection( $BookingRequests);
             return response()->json([
              'errors'=>'you don\'t have any Book request for this house'],200);

}
public function acceptedeBooking($id)
{
    try{
    $booking = Book::findOrFail($id);
    $owner_id=Auth::id();
    $houseId=$booking->house_id;
    $userId=$booking->user_id;
    $ownerId=House::select('user_id')->where('id',$houseId)->first();
    if ($owner_id!==$ownerId->user_id)
         return response()->json(['errors' => 'unauthorized'], 403);
    if ($booking->book_status==='pending')
    {
        $user=User::findOrFail($userId);
        $owner=User::findOrFail($owner_id);
        if ($booking->total_price>$user->account){
            return response()->json([
            "errors" => "The money in this account is not enough for the booking price."
        ], 422);
        }
        $user->account-=$booking->total_price;
        $user->save();
        $owner->account+=$booking->total_price;
        $owner->save();
        $booking->update([
        'book_status' => 'accepted'
    ]);
     Book::where('house_id', $booking->house_id)
        ->where('id','!=', $booking->id)
        ->where('book_status', 'pending')
        ->where(function ($q) use ($booking) {
            $q->whereBetween('start_date', [$booking->start_date, $booking->end_date])
              ->orWhereBetween('end_date', [$booking->start_date, $booking->end_date])
              ->orWhere(function ($query) use ($booking) {
                    $query->where('start_date', '<=', $booking->start_date)
                          ->where('end_date', '>=', $booking->end_date);
                });
        })
        ->update(['book_status' => 'cancelled']);
      Book::where('house_id', $houseId)
     ->where('id','!=', $booking->id)
     ->where('start_date_update' ,'!=',null)
     ->where('end_date_update' ,'!=',null)
          ->where('price_difference' ,'!=',null)
          ->where('total_price_update' ,'!=',null)
           ->where(function ($q) use ($booking) {
            $q->whereBetween('start_date_update', [$booking->start_date, $booking->end_date])
              ->orWhereBetween('end_date_update', [$booking->start_date, $booking->end_date])
              ->orWhere(function ($query) use ($booking) {
                    $query->where('start_date_update', '<=', $booking->start_date)
                          ->where('end_date_update', '>=', $booking->end_date);
                });
        })
             ->update(['start_date_update' => null,
                'end_date_update'=>null,
                'price_difference'=>null,
                'total_price_update'=>null
            ]);



    return response()->json(['message' => 'Booking accepted ']);
}
    return response()->json(['errors'=>'this book is '.$booking->book_status .' already']);
    }
    catch (ModelNotFoundException $ex) {
        return response()->json([
            'errors'  => 'this book not found',
            'details' => $ex->getMessage()
        ], 404);

}
}
public function RejectionBooking($id){
    try{
    $booking = Book::findOrFail($id);
    $owner_id=Auth::id();
    $houseId=$booking->house_id;
    $ownerId=House::select('user_id')->where('id',$houseId)->first();
    if ($owner_id!==$ownerId->user_id)
         return response()->json(['errors' => 'unauthorized'], 403);
    if ($booking->book_status==='pending')
    {
        $booking->update([
        'book_status' => 'cancelled'
    ]);
    return response()->json(['message' => 'Booking cancelled ']);}
    return response()->json(['errors'=>'this book is '.$booking->book_status .' already']);}
    catch (ModelNotFoundException $ex) {
        return response()->json([
            'errors'  => 'this book not found',
            'details' => $ex->getMessage()
        ], 404);

}
}
public function cancelBooking($Id) {
    $booking = Book::findOrFail($Id);
    $user = $booking->user;
    $userId = $booking->user_id;
    $user_id=Auth::id();
    $houseId=$booking->house_id;
    $ownerId=House::select('user_id')->where('id',$houseId)->first()->user_id;
    $owner=User::findOrFail($ownerId);
    if($userId!==$user_id){
        return response()->json(['errors' => 'unauthorized'], 403);
    }
        $daysBeforeStart = (strtotime($booking->start_date) - strtotime('now')) / (60*60*24);
        $daysBeforeStart = (int)$daysBeforeStart;
    if ($booking->book_status === 'accepted') {

        if ($daysBeforeStart >= 5) {
            $user->account += $booking->total_price;
            $user->save();
            $owner->account -= $booking->total_price;
            $owner->save();
        }
        else if ($daysBeforeStart >= 1){
            $user->account += $booking->total_price/2;
            $user->save();
            $owner->account -= $booking->total_price/2;
            $owner->save();
        }

    }
    $booking->update([
        'book_status' => 'cancelled'
    ]);

    return response()->json(['message' => 'Booking cancelled successfully']);
}
public function getMyBookings(Request $request)
{
    $userId = Auth::id();
    $now = now();

    $type = $request->query('type');

    $bookings = Book::where('user_id', $userId)
        ->when($type === 'pending', function ($query) {
            $query->where('book_status', 'pending');
        })
        ->when($type === 'current', function ($query) use ($now) {
            $query->where('book_status', 'accepted')
                  ->where('start_date','<=',$now)
                  ->where('end_date', '>=', $now);
        })
        ->when($type === 'cancelled', function ($query) use ($now) {
            $query->where(function ($q) use ($now) {
                $q->where('book_status', 'cancelled')
                  ->orWhere('end_date', '<', $now);
            });
        })
        ->orderBy('start_date', 'desc')
        ->get();

    return BookResource::collection($bookings);
}
public function updateBooking(Request $request, $id)
{
    try{
    $request->validate([
    'start_date_update' => 'required|date',
    'end_date_update'   => 'required|date|after:start_date_update',
    ]);
    $booking = Book::findOrFail($id);
    $user = $booking->user;
    $house = $booking->house;
    $userId = $booking->user_id;
    $user_id=Auth::id();
    $houseId=$booking->house_id;
    $ownerId=House::select('user_id')->where('id',$houseId)->first()->user_id;
    $owner=User::findOrFail($ownerId);
    if($userId!==$user_id){
        return response()->json(['errors' => 'unauthorized'], 403);
    }
    $oldStartDate = date('Y-m-d', strtotime($booking->start_date));
    $oldEndDate = date('Y-m-d', strtotime($booking->end_date));

    $newStartDate = date('Y-m-d', strtotime($request->start_date_update));
    $newEndDate = date('Y-m-d', strtotime($request->end_date_update));

if ($oldStartDate === $newStartDate && $oldEndDate === $newEndDate) {
    return response()->json(['errors' => 'no change'], 422);
}


    if ($booking->book_status === 'cancelled') {
        return response()->json([
            'errors' => 'Cancelled booking cannot be updated'
        ], 403);
    }

    if (now()->greaterThan($booking->end_date)) {
        return response()->json([
            'errors' => 'Finished booking cannot be updated'
        ], 403);
    }

    $overlap = Book::where('house_id', $house->id)
    ->where('id', '!=', $booking->id)
    ->where('book_status','accepted')
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->start_date_update, $request->end_date_update])
                  ->orWhereBetween('end_date', [$request->start_date_update, $request->end_date_update])
                  ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date_update)
                          ->where('end_date', '>=', $request->end_date_update);
                  });
        })
        ->exists();

    if ($overlap) {
        return response()->json([
           "erorrs" => "This house is already booked for that period."
        ], 422);
    }

    $oldStart = new DateTime($booking->start_date);
    $oldEnd = new DateTime($booking->end_date);
    $oldDays = $oldEnd->diff($oldStart)->days+1;
    $oldPrice = $oldDays * $house->day_price;

    $newStart = new DateTime($request->start_date_update);
    $newEnd = new DateTime($request->end_date_update);
    $newDays = $newEnd->diff($newStart)->days+1;
    $newPrice = $newDays * $house->day_price;
    $priceDifference = $newPrice - $oldPrice;

 if ($booking->book_status === 'accepted') {


    if ($priceDifference > 0) {
        if ($priceDifference > $user->account) {
            return response()->json([
                "errors" => "The money in your account is not enough for the booking price."
            ], 422);
        }

       /* $user->account -= $priceDifference;
        $user->save();
        $owner->account += $priceDifference;
        $owner->save();*/

    }
    if ($priceDifference < 0) {
        $daysBeforeStart = (strtotime($booking->start_date) - strtotime('now')) / (60*60*24);
        $daysBeforeStart = (int)$daysBeforeStart;
        if ($daysBeforeStart >= 5) {
           /* $owner->account -=abs($priceDifference);
            $owner->save();
            $user->account += abs($priceDifference);
            $user->save();*/
        }
        else if ($daysBeforeStart >= 1){

          /*  $owner->account -= abs($priceDifference)/2;
            $owner->save();
            $user->account += abs($priceDifference)/2;
            $user->save();*/
            $priceDifference/=2;

        }
        else{
           $priceDifference=0;
        }
    }


}

    $booking->update([
        'start_date_update' => $request->start_date_update,
        'end_date_update'   => $request->end_date_update,
        'total_price_update'=> $newPrice,
        'price_difference'=> $priceDifference
    ]);
   return response()->json(  ['Book'=>[
                'id'=>$booking->id,
                'user_id' => $booking->user_id,
                'house_id' => $booking->house_id,
                'start_date_update' => $booking->start_date_update,
                'end_date_update' => $booking->end_date_update,
                'price_difference'=>$priceDifference
            ]], 200);
}
catch (ModelNotFoundException $ex) {
        return response()->json([
            'errors'  => 'this book not found',
            'details' => $ex->getMessage()
        ], 404);
}
}
public function updateRequests($houseId)
{
     $updateRequests= Book::where('house_id', $houseId)
     ->where('start_date_update' ,'!=',null)
     ->where('end_date_update' ,'!=',null)
    ->where('price_difference' ,'!=',null)
     ->where('total_price_update' ,'!=',null)->get();

      $user_id=Auth::id();
    $userId=House::select('user_id')->where('id',$houseId)->first();
    if ($user_id!==$userId->user_id)
         return response()->json(['errors' => 'unauthorized'], 403);
    if (count( $updateRequests)!==0)
         return UpdateBookeResourse::collection($updateRequests);
     return response()->json([
              'errors'=>'you don\'t have any update request for this house'],200);



}
public function acceptedUpdateBookRequest($id){
 try{
    $booking = Book::findOrFail($id);
    $user = $booking->user;
    $priceDifference=$booking->price_difference;
    $owner_id=Auth::id();
    $houseId=$booking->house_id;
    $userId=$booking->user_id;
    $ownerId=House::select('user_id')->where('id',$houseId)->first()->user_id;
    $owner=User::findOrFail($ownerId);
    if ($owner_id!==$ownerId)
         return response()->json(['errors' => 'unauthorized'], 403);
         if ($priceDifference > 0) {
        if ($priceDifference > $user->account) {
            return response()->json([
                "errors" => "The money in your account is not enough for the booking price."
            ], 422);
        }

        $user->account -= $priceDifference;
        $user->save();
        $owner->account += $priceDifference;
        $owner->save();
}
 if ($priceDifference < 0) {
            $owner->account -= abs($priceDifference);
            $owner->save();
            $user->account += abs($priceDifference);
            $user->save();
    }

     Book::where('house_id', $booking->house_id)
        ->where('id','!=', $booking->id)
        ->where('book_status', 'pending')
        ->where(function ($q) use ($booking) {
            $q->whereBetween('start_date', [$booking->start_date_update, $booking->end_date_update])
              ->orWhereBetween('end_date', [$booking->start_date_update, $booking->end_date_update])
              ->orWhere(function ($query) use ($booking) {
                    $query->where('start_date', '<=', $booking->start_date_update)
                          ->where('end_date', '>=', $booking->end_date_update);
                });
        })
     ->update(['book_status' => 'cancelled']);
      Book::where('house_id', $houseId)
     ->where('id','!=', $booking->id)
     ->where('start_date_update' ,'!=',null)
     ->where('end_date_update' ,'!=',null)
     ->where('price_difference' ,'!=',null)
     ->where('total_price_update' ,'!=',null)
     ->where(function ($q) use ($booking) {
            $q->whereBetween('start_date_update', [$booking->start_date_update, $booking->end_date_update])
              ->orWhereBetween('end_date_update', [$booking->start_date_update, $booking->end_date_update])
              ->orWhere(function ($query) use ($booking) {
                    $query->where('start_date_update', '<=', $booking->start_date_update)
                          ->where('end_date_update', '>=', $booking->end_date_update);
                });
        })
             ->update(['start_date_update' => null,
                'end_date_update'=>null,
                'price_difference'=>null,
                'total_price_update'=>null
            ]);
        $booking->update([
        'start_date' => $booking->start_date_update,
        'end_date'   => $booking->end_date_update,
        'total_price'=> $booking->total_price_update,
        'start_date_update' => null,
        'end_date_update'   => null,
        'total_price_update'=> null,
        'price_difference'=> null
    ]);
      return response()->json(['message' => ' update Booking accepted ']);


}
catch (ModelNotFoundException $ex) {
        return response()->json([
            'errors'  => 'this book not found',
            'details' => $ex->getMessage()
        ], 404);

}
}
public function RejectionUpdateBookRequest($id){
    try{
    $booking = Book::findOrFail($id);
    $owner_id=Auth::id();
    $houseId=$booking->house_id;
    $ownerId=House::select('user_id')->where('id',$houseId)->first();
    if ($owner_id!==$ownerId->user_id)
    {
         return response()->json(['errors' => 'unauthorized'], 403);

    }
        $booking->update(['start_date_update' => null,
                'end_date_update'=>null,
                'price_difference'=>null,
                'total_price_update'=>null
            ]);
    return response()->json(['message' => ' update Booking Rejection ']);}
    catch (ModelNotFoundException $ex) {
        return response()->json([
            'errors'  => 'this book not found',
            'details' => $ex->getMessage()
        ], 404);

}
}
public function getBookForHouse($id)
{
     $bookings=BooK::where('house_id',$id)->where('book_status','accepted')->get();
     return BookResource::collection($bookings);
}
}
