<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Transaction;

class WalletController extends Controller
{
    
        // Deposit money into the user's wallet
        public function deposit(Request $request)
        {
            $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);
    
            $user = $request->user();
            $wallet = $user->wallet;
    
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0
                ]);
            }
    
            // Deposit money
            $wallet->deposit($request->amount);
    
            // Log the transaction
            $wallet->transactions()->create([
                'type' => 'Deposit',
                'amount' => $request->amount,
                'description' => $request->description,
                'transaction_id' => $request->transaction_id,
                'payment_gateway' => $request->payment_gateway,
                'transaction_type' => 'Deposit'
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Deposit successful',
                'balance' => $wallet->balance,
            ], 200);
        }
    
        // Get all transactions of the user
        public function getTransactions(Request $request)
        {
            $user = $request->user();
            $wallet = $user->wallet;
    
            if (!$wallet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No wallet found',
                ], 404);
            }
    
            $transactions = $wallet->transactions()->latest()->paginate(6);
    
            return response()->json([
                'status' => 'success',
                'data' => $transactions,
            ], 200);
        }

        public function viewTransaction($id) {
            
            $transaction = Transaction::where('id', $id)->first();

            return response()->json([
                'status' => 'success',
                'data' => $transaction,
            ], 200);

        }


public function getBalance(Request $request)
{
    $user = $request->user();
    $wallet = $user->wallet;

    if (!$wallet) {
        return response()->json([
            'status' => 'error',
            'message' => 'No wallet found',
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'balance' => $wallet->balance,
    ], 200);
}

// Withdraw money from the user's wallet
public function withdraw(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
    ]);

    $user = $request->user();
    $wallet = $user->wallet;

    if (!$wallet) {
        return response()->json([
            'status' => 'error',
            'message' => 'No wallet found',
        ], 404);
    }

    if ($wallet->balance < $request->amount) {
        return response()->json([
            'status' => 'error',
            'message' => 'Insufficient balance for this withdrawal',
        ], 400);
    }

    // Withdraw money
    $wallet->balance -= $request->amount;
    $wallet->save();

    // Log the transaction
    $wallet->transactions()->create([
        'type' => 'withdrawal',
        'amount' => $request->amount,
        'description' => 'Withdrawal from wallet',
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Withdrawal successful',
        'balance' => $wallet->balance,
    ], 200);
}
    
}
