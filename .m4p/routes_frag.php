
Route::middleware(['auth:sanctum', 'api.locale'])->group(function () {
    Route::get('/invoices', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'index']);
    Route::post('/invoices', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'create']);
    Route::get('/invoices/{id}', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'show']);
    Route::get('/invoices/{id}/items', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'itemsIndex']);
    Route::post('/invoices/{id}/items', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'createItem']);
    Route::put('/invoices/{invoiceId}/items/{itemId}', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'updateItem']);
    Route::delete('/invoices/{invoiceId}/items/{itemId}', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'deleteItem']);
});

Route::middleware(['auth:sanctum', 'api.locale'])->group(function () {
    Route::get('/invoice-settings', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'settingsIndex']);
    Route::post('/invoice-settings', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'createSetting']);
    Route::put('/invoice-settings/tax', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'updateTaxSetting']);
    Route::put('/invoice-settings/{id}', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'updateSetting']);
    Route::delete('/invoice-settings/{id}', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'deleteSetting']);
});
