<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\OTP;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
{
    
    
    public function resendOTP(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'email'                => ['required', 'string', 'email', 'max:191'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 422,
                    'message' => 'Email Required',
                ],
            ], 422);
        }
        
        $user = User::where('email', $request->email)->first();
        
        if($user == null) {
             return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 404,
                    'message' => 'Unknown User Register',
                ],
            ], 422);
        }
        
        $otp = $user->generateOTP();
        
        $user->generateOTP();
        //$user->notify(new OTP());

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
            ],
            'message' => 'OTP regenerated succesfully',
        ], 200);
        
        
    }
    
    public function verifyOTP(Request $request) {
        $validator = Validator::make($request->all(), [
            'otp'                => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 422,
                    'message' => 'This field is required',
                ],
            ], 422);
        }
        
        $user = User::where('otp', $request->otp)->first();
        
        if($user == null) {
             return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 404,
                    'message' => 'OTP not found try resending',
                ],
            ], 422);
        }

        if($user->otp == null) {
            return response()->json([
               'status' => 'error',
               'error' => [
                   'code' => 404,
                   'message' => 'The force are watching you',
               ],
           ], 422);
       }
        
        $user->email_verified_at = now();
        $user->otp = null;
        $user->save();
        
        $random = mt_rand(100000, 999999);
        $token = $user->createToken($random)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                "token" => $token,
            ],
            'message' => "OTP Verified Successfull",
        ], 200); 
        
    }



}
