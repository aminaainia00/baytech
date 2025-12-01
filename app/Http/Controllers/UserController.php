<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    public function register(Request $request){

        $request->validate([
            'first_name'=>'required|string|max:20|min:3',
            'last_name'=>'required|string|max:20|min:3',
            'date_of_birth'=>'required|date|before_or_equal:today',
            'phone'=>'unique:users,phone|string|required|min:10|max:10',
            'password'=>['required','string','confirmed',Password::min(8)->mixedCase()->numbers()->symbols()],
            'Personal_identity_photo'=>'required|image|mimes:png,jpg,jpeg,gef|max:2048',
            'personal_photo'=>'required|image|mimes:png,jpg,jpeg,gef|max:2048'
        ],
        [
          'date_of_birth.date'=>' we need the full date'
        ]);
        $validated=[
            'first_name'=>$request->first_name,
            'last_name'=> $request->last_name,
            'date_of_birth'=>$request->date_of_birth,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password)];

    if($request->hasFile('Personal_identity_photo'))
               {
                 $path= $request->file('Personal_identity_photo')->store('my photo','public');
                 $validated['Personal_identity_photo']=$path;
               }
             if($request->hasFile('personal_photo'))
               {
                 $path= $request->file('personal_photo')->store('my photo','public');
                 $validated['personal_photo']=$path;
               }
        $user=User::create($validated);
        return response()->json($user, 201);

    }


    public function registerAdmin(Request $request){

        $request->validate([
            'first_name'=>'required|string|max:20|min:3',
            'last_name'=>'required|string|max:20|min:3',
            'phone'=>'unique:users,phone|string|required|min:10|max:10',
            'password'=>['required','string','confirmed',Password::min(8)->mixedCase()->numbers()->symbols()]
        ]);
        $validated=[
            'first_name'=>$request->first_name,
            'last_name'=> $request->last_name,
            'phone'=>$request->phone,
            'role'=>'admin',
            'active'=>'active',
            'password'=>Hash::make($request->password)];
        $user=User::create($validated);
        return response()->json($user, 201);

    }


    public function getUser($userId){
        try{
            $userData=User::findOrFail($userId);
           return response()->json($userData,200);
           }
        catch(ModelNotFoundException $ex){
            return response()->json([
              'message'=>'this user not found',
              'details'=>$ex->getMessage()],404);}

    }


    public function getRequests(){
        $usersnotactive=User::select('id','first_name','last_name')->where('active','notactive')->get();

        return  UserResource::collection($usersnotactive);
    }
    public function acceptedUser($userId){
        try{
            $userData=User::findOrFail($userId);
            $userData->update(['active'=>'active']);
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
            'phone'=>'string|required|min:10|max:10',
            'password'=>['required','string',Password::min(8)->mixedCase()->numbers()->symbols()]

        ]);
        if(!Auth::attempt($request->only('phone','password')))
        return response()->json([
            'message'=>'invalid phone or password'
        ], 401
    );

    $user=User::where('phone',$request->phone)->firstOrFail();
    $token=$user->createToken('auth_Token')->plainTextToken;
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

}

