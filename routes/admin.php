


Route::post('/currencies', [CurrencyController::class, 'store']); // Add new currency
Route::put('/currencies/{id}', [CurrencyController::class, 'update']); // Update currency conversion rate
Route::put('/currencies/{id}/toggle-active', [CurrencyController::class, 'toggleActiveStatus']); // Toggle active status
Route::get('/currencies', [CurrencyController::class, 'index']); // Get list of all currencies
Route::get('/currencies/active', [CurrencyController::class, 'getActiveCurrencies']); // Get list of active currencies