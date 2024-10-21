<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    
        // List all currencies
        public function index()
        {
            $currencies = Currency::all();
            return response()->json($currencies, 200);
        }
    
        // Store a new currency
        public function store(Request $request)
        {
            $validated = $request->validate([
                'currency_code' => 'required|unique:currencies|max:3',
                'conversion_rate' => 'required|numeric|min:0.00001',
                'is_active' => 'boolean',
            ]);
    
            $currency = Currency::create($validated);
            return response()->json($currency, 201);
        }
    
        // Show a specific currency
        public function show($id)
        {
            $currency = Currency::findOrFail($id);
            return response()->json($currency, 200);
        }
    
        // Update a currency
        public function update(Request $request, $id)
        {
            $validated = $request->validate([
                'conversion_rate' => 'numeric|min:0.00001',
                'is_active' => 'boolean',
            ]);
    
            $currency = Currency::findOrFail($id);
            $currency->update($validated);
    
            return response()->json(['message' => 'Currency updated successfully'], 200);
        }
    
        // Toggle the active status of a currency
        public function toggleActiveStatus($id)
        {
            $currency = Currency::findOrFail($id);
            $currency->is_active = !$currency->is_active;
            $currency->save();
    
            return response()->json(['message' => 'Currency status updated'], 200);
        }
    
        // Delete a currency
        public function destroy($id)
        {
            $currency = Currency::findOrFail($id);
            $currency->delete();
    
            return response()->json(['message' => 'Currency deleted successfully'], 200);
        }

}
