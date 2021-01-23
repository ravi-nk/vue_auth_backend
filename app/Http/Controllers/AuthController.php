<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        
            $data = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
        
            $user = User::where('email', $request->email)->first();
        
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'message' => ['The provided credentials are incorrect.']
                ], 404);
            }
        
            $token = $user->createToken('my-app-token')->plainTextToken;
        
            $response = [
                'user' => $user,
                'token' => $token
            ];

            if($user->is_admin == 1){
                return response()->json($user);
            }
            
        
            return response($response, 201);
        }

        public function register(Request $request){
            $user = User::where('email',$request->email)->first();
            if(isset($user->id)){
                return response()->json('User already exits',401);
            }
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
        
            Auth::login($user);
            
            return response()->json($user);
            }
            public function users(){
                $users = User::where("name","!=","Admin")->get();
                return response()->json($users);
            }
          
}
