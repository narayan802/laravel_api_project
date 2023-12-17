<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    //Register Api (POST)
    public function register(Request $request){
        //Data validate
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed',
        ]);
       

        //Create User
       $user= User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=> Hash::make($request->password),
           
        ]);
        if ($user) {
            $data = [ 
                'user' => $user,
                'status' => 200,
                'message' => 'User Regiter Sucessfully',
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'status' => 404,
                'message' => 'User Not Regiter',
            ];
            return response()->json($data,404);
        }
    }

    //Login Api (POST)
    public function login(Request $request){
        $request->validate([
            
            'email'=>'required|email',
            'password'=>'required',
        ]);
      if( Auth::attempt([
            'email'=>$request->email,
            'password'=>$request->password
        ])){
            $user = Auth::User();
            $accessToken = $user->createToken('authToken')->accessToken;
            $data = [
                'access_token' => $accessToken,
                'status' => 200,
                'message' => 'Login Sucessfully',
            ];
            return response()->json($data, 200);
            
        }else{
            $data = [
                'status' => 401,
                'message' => 'Invalid Cradincial'
            ];
            return response()->json($data, 401);
        }
    }

    //Profile Api (GET)
    public function profile(){
        $user = Auth::User();
        // dd($user);
        $data = [
            'user'=>$user,
            'status' => 200,
            'message' => 'Profile Information',
        ];
        return response()->json($data, 200);
        
    }

    //Logout Api (GET)
    public function logout(){
        // $user = Auth::User();
        auth()->User()->token()->revoke();
        $data = [
            'status' => 200,
            'message' => 'User Logout'
        ];
        return response()->json($data, 200);

    }
 
}