<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Wallet;
use App\Models\Transaction;

class ServiceController extends Controller
{
    public function listService() {

                // Define the API endpoint
                $url = 'https://sandbox.vtpass.com/api/service-categories';

                // Set your API and public keys
                $apiKey = 'a39e799a687ba09d141c3fcfca784b2e';
                $publicKey = 'PK_906afc24d5182dcb628b948e00b0a70a8e8a45c8e11';
        
                // Make the API call with headers
                $response = Http::withHeaders([
                    'api-key' => $apiKey,
                    'public-key' => $publicKey,
                ])->get($url);

                return $response;
    }

    public function listServiceProvider($identifier) {

        // Define the API endpoint
        $url = "https://sandbox.vtpass.com/api/services?identifier={$identifier}";

        // Set your API and public keys
        $apiKey = 'a39e799a687ba09d141c3fcfca784b2e';
        $publicKey = 'PK_906afc24d5182dcb628b948e00b0a70a8e8a45c8e11';

        // Make the API call with headers
        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'public-key' => $publicKey,
        ])->get($url);

        return $response;
}

public function listServiceVariation($service) {

    // Define the API endpoint
    $url = "https://sandbox.vtpass.com/api/service-variations?serviceID={$service}";

    // Set your API and public keys
    $apiKey = 'a39e799a687ba09d141c3fcfca784b2e';
    $publicKey = 'PK_906afc24d5182dcb628b948e00b0a70a8e8a45c8e11';

    // Make the API call with headers
    $response = Http::withHeaders([
        'api-key' => $apiKey,
        'public-key' => $publicKey,
    ])->get($url);

    return $response;
}

public function payService(Request $request) {    

        // Define the API endpoint
        $url = "https://sandbox.vtpass.com/api/pay";

        // Set your API and secret keys
        $apiKey = 'a39e799a687ba09d141c3fcfca784b2e';
        $secretKey = 'SK_98211bd63250edd280a9374fde5d931f482e578d7ef'; // Replace 'public-key' with 'secret-key'
    
        // Data to send in the POST request
        $postData = [
            // Include any required fields for the POST request
            'serviceID' => $request->service_id, // Replace with your actual data
            'request_id' => $request->request_id,
            'amount' => $request->amount,
            'phone' => $request->phone,
        ];
    
        // Make the POST request with headers
        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'secret-key' => $secretKey,
        ])->post($url, $postData);

        $vtu_response = $response->json();

        if( $vtu_response['code'] == 000) {
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
    
        $wallet->balance -= $request->amount;
        $wallet->save();
        
        // Log the transaction
        $wallet->transactions()->create([
            'type' => 'withdraw',
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_id' => $request->transaction_id,
            'payment_gateway' => 'online',
            'transaction_type' => $request->transaction_type,
        ]);

        $transaction = $wallet->transactions()->latest()->first();

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction,
            'vtu_response' => $response->json()
        ], 200);
        }

        return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 422,
                    'message' => 'Unable to process data',
                ],
        ], 422);   
        
        //return $response->json(); // Return the response as JSON    
}


}
