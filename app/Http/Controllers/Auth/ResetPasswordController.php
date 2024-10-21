<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\OTP;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'otp'                => ['required'],
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

        $user = User::where('otp', $request->otp)->first();
        
        if($user == null) {
             return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 404,
                    'custom_message' => 'OTP not found try again',
                ],
            ], 422);
        }

        if($user->otp == null) {
            return response()->json([
               'status' => 'error',
               'error' => [
                   'code' => 404,
                   'custom_message' => 'The force are watching you please insert OTP',
               ],
           ], 422);
       }

       if($user->otp_expires_at >=  now() ) {
        return response()->json([
           'status' => 'error',
           'error' => [
               'code' => 404,
               'custom_message' => 'OTP Expired',
           ],
       ], 422);
       }

        if ($user) {
            // Update the user's password
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $user,
                ],
                'message' => 'User created successfully',
            ], 200);
        }

    }
}
