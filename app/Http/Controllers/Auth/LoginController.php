<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 422,
                    'message' => $validator->errors(),
                ],
            ], 422);      
        }
    
        $user = User::where('email', $request->email)->first();
        
        if($user == NULL) {
            return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 401,
                    'message' => 'Invalid Credential please try registering',
                ],
            ], 401);  
        }
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 401,
                    'message' => 'Invalid Credentials',
                ],
            ], 401);   
        }

        $random = mt_rand(100000, 999999);
        $token = $user->createToken($random)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                "token" => $token,
            ],
            'message' => "Login Succesfull",
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => "Logout Succesfull",
        ], 200);

    }

}
