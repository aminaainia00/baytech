<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\RegisterRequestNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'first_name'=>'required|string|max:20|min:3',
            'last_name'=>'required|string|max:20|min:3',
            'date_of_birth'=>'required|date|before_or_equal:today',
            'phone'=>'unique:users,phone|required|min:10|max:10|regex:/^[0-9]+$/',
            'password'=>['required','string','confirmed',Password::min(8)->mixedCase()->numbers()->symbols()],
            'Personal_identity_photo'=>'required|image|mimes:png,jpg,jpeg,gef|max:10240',
            'personal_photo'=>'required|image|mimes:png,jpg,jpeg,gef|max:10240'
        ],
        [
          'date_of_birth.date'=>' we need the full date',
           'phone.unique'=>'The phone number has already been taken',
           'phone.min' =>'The phone number field must be at least 10 characters.',
           'phone.max'=>'The phone number field must not be greater than 10 characters.',
           'phone.regex' => 'The phone number must contain numbers only.',
           'phone.required'=>'The phone number field is required.'
        ]);
        $validated=[
            'first_name'=>$request->first_name,
            'last_name'=> $request->last_name,
            'date_of_birth'=>$request->date_of_birth,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password)];

            if($request->hasFile('Personal_identity_photo'))
               {
                 $path= $request->file('Personal_identity_photo')->store('my_photo','public');
                 $validated['Personal_identity_photo']=$path;
               }
            if($request->hasFile('personal_photo'))
               {
                 $path= $request->file('personal_photo')->store('my_photo','public');
                 $validated['personal_photo']=$path;
               }
        $user=User::create($validated);
        $users=User::where('role','admin')->get();
        Notification::send($users,new RegisterRequestNotification($user));
        return response()->json(['message'=>'Your request is being processed','userData'=>$user], 201);
    }


    public function registerAdmin(Request $request){
        $request->validate([
            'first_name'=>'required|string|max:20|min:3',
            'last_name'=>'required|string|max:20|min:3',
            'phone'=>'unique:users,phone|required|min:10|max:10|regex:/^[0-9]+$/',
            'password'=>['required','string','confirmed',Password::min(8)->mixedCase()->numbers()->symbols()]
        ],
        ['phone.unique'=>'The phone number has already been taken',
           'phone.min' =>'The phone number field must be at least 10 characters.',
           'phone.max'=>'The phone number field must not be greater than 10 characters.',
           'phone.regex' => 'The phone number must contain numbers only.',
           'phone.required'=>'The phone number field is required.']);
        $validated=[
            'first_name'=>$request->first_name,
            'last_name'=> $request->last_name,
            'phone'=>$request->phone,
            'role'=>'admin',
            'active'=>'active',
            'password'=>Hash::make($request->password)];
        $user=User::create($validated);
        return response()->json(['message'=>'welcome admin','userData'=>$user], 201);

    }


    public function getUser($userId){
        try{
           $userData=User::findOrFail($userId);
           return response()->json(['User'=>$userData],200);
           }
        catch(ModelNotFoundException $ex){
            return response()->json([
              'message'=>'this user not found',
              'details'=>$ex->getMessage()],404);}

    }


     public function getUserForActive(){
           $userData=Auth::user();
           return response()->json(['User'=>$userData],200);
    }

    public function getRegisterRequests(){
        $usersnotactive=User::select('id','first_name','last_name','personal_photo')->where('active','notactive')->get();
        return  UserResource::collection($usersnotactive);
    }


    public function acceptedUser($userId){
        try{
            $userData=User::findOrFail($userId);
            $userData->update(['active'=>'active']);
            return response()->json(['message'=>'the user accepted'], 200);
           }
        catch(ModelNotFoundException $ex){
        return response()->json([
              'message'=>'this user not found',
              'details'=>$ex->getMessage()],404);
           }
        }


       public function rejectionUser($userId)
         {
        try{
        $userData=User::findOrFail($userId);
        $userData->delete();
         return response()->json(['message'=>'the user  rejected'], 200);
        }catch(ModelNotFoundException $e)
        {
            return response()->json([
                'Error'=>'user not found',
                'details'=>$e->getMessage()

            ], 404);


        }

    }


    public function login(Request $request)
    {
         $request->validate([
            'phone'=>'required|min:10|max:10|regex:/^[0-9]+$/',
            'password'=>['required','string',Password::min(8)->mixedCase()->numbers()->symbols()]

        ],
        [
           'phone.min' =>'The phone number field must be at least 10 characters.',
           'phone.max'=>'The phone number field must not be greater than 10 characters.',
           'phone.regex' => 'The phone number must contain numbers only.',
           'phone.required'=>'The phone number field is required.'
        ]);
        if(!Auth::attempt($request->only('phone','password')))
        return response()->json([
            'message'=>'invalid phone or password'
        ], 401
    );

    $user=User::where('phone',$request->phone)->firstOrFail();
    $token=$user->createToken('auth_Token')->plainTextToken;
    $user->fcm_token=$request->fcm_token;
    $user->save();
     return response()->json([
            'message'=>'user login successfully',
            'User'=>$user,
            'Token'=>$token
        ], 201 );

    }

    public function logout(Request $request)
    {
           $request->user()->currentAccessToken()->delete();

            return response()->json([
            'message'=>'user logout successfully',

        ] );
    }
    public function requestDelete(){
        $user_id=Auth::user()->id;
        $userData=User::findOrFail($user_id);
        $userData->update(['delete'=>'true']);
          return response()->json([
            'message'=>'Your request is being processed'
          ],200
     );
    }
    public function getDeleteRequests(){
        $user_delete=User::select('id','first_name','last_name','personal_photo')->where('delete','true')->get();

        return  UserResource::collection($user_delete);
    }
    public function acceptedDeleteUser($userId)
    {
        try{
        $userData=User::findOrFail($userId);
        $userData->delete();
        return response()->json(['message'=>'the user was deleted'], 200);
        }catch(ModelNotFoundException $e)
        {
            return response()->json([
                'Error'=>'user not found',
                'details'=>$e->getMessage()

            ], 404);

        }



    }
     public function rejectionDeleteUser($userId){
        try{
            $userData=User::findOrFail($userId);
            $userData->update(['delete'=>'false']);
            return response()->json(['message'=>'the user was not deleted'], 200);
           }
        catch(ModelNotFoundException $ex){
        return response()->json([
              'message'=>'this user not found',
              'details'=>$ex->getMessage()],404);
           }
        }
        public function addToAccount(Request $request){
            $request->validate([
            'phone'=>'required|min:10|max:10|regex:/^[0-9]+$/',
            'account'=>'required|regex:/^[0-9]+$/'
             ],
             [
           'phone.min' =>'The phone number field must be at least 10 characters',
           'phone.max'=>'The phone number field must not be greater than 10 characters',
           'phone.regex' => 'The phone number must contain numbers only.',
           'phone.required'=>'The phone number field is required.'
            ]);
            try{
            $userData=User::where('phone',$request->phone)->firstOrFail();
            $useraccount= $userData->account;
            $userData->update([
            'account' =>$useraccount+ $request->account
            ]);
               return response()->json([
                'message' => 'Account updated successfully'], 200);

            }
            catch(ModelNotFoundException $ex){
              return response()->json([
                'Error'=>'the phone not found',
                'details'=>$ex->getMessage()

            ], 404);
            }

        }


}

