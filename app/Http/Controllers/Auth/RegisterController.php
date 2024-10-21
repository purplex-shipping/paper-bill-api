<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\OTP;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class RegisterController extends Controller
{
    
    public function store(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'name' => ['required', 'string', 'max:191'],
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

        // Generate UUID
        $uuid = Uuid::uuid4()->toString();
        
        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->slug = $uuid;
        $user->save();

        $random = mt_rand(100000, 999999);
        $token = $user->createToken($random)->plainTextToken; 
        
        $otp = $user->generateOTP();
        $user->generateOTP();
        $user->notify(new OTP());

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                "token" => $token,
            ],
            'message' => 'User created successfully',
        ], 200);

    }

}
 