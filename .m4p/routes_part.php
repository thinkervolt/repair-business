
    Route::get('/invoices', [InvoiceController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/invoices', [InvoiceController::class, 'create'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/invoices/{id}/items', [InvoiceController::class, 'itemsIndex'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/invoices/{id}/items', [InvoiceController::class, 'createItem'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/invoices/{invoiceId}/items/{itemId}', [InvoiceController::class, 'updateItem'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/invoices/{invoiceId}/items/{itemId}', [InvoiceController::class, 'deleteItem'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/invoice-settings', [InvoiceController::class, 'settingsIndex'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/invoice-settings', [InvoiceController::class, 'createSetting'])->middleware(['auth:sanctum', 'admin', 'api.locale']);
    Route::put('/invoice-settings/{id}', [InvoiceController::class, 'updateSetting'])->middleware(['auth:sanctum', 'admin', 'api.locale']);
    Route::delete('/invoice-settings/{id}', [InvoiceController::class, 'deleteSetting'])->middleware(['auth:sanctum', 'admin', 'api.locale']);
    Route::put('/invoice-settings/tax', [InvoiceController::class, 'updateTaxSetting'])->middleware(['auth:sanctum', 'admin', 'api.locale']);
