<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthenticateController extends Controller
{

    public function loginUser(Request $request)
    {

        $user = $request->user();

        $wallet = $user->wallet;
        $balance = $wallet ? $wallet->balance : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $request->user(),
                'balance' => $balance,
            ],
            'message' => "Login User Info",
        ], 200);
        
    }

}
