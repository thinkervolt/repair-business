<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BarcodeController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LogController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\RepairController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\TrashController;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('api.locale');
    Route::get('/public/business-profile', [SettingController::class, 'publicProfile'])->middleware('api.locale');
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/auth/email/verify', [AuthController::class, 'verifyEmail'])->middleware('api.locale');
    Route::post('/auth/email/resend', [AuthController::class, 'resendVerification'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/auth/password/email', [AuthController::class, 'forgotPassword'])->middleware('api.locale');
    Route::post('/auth/password/reset', [AuthController::class, 'resetPassword'])->middleware('api.locale');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/notifications', [NotificationController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/customers', [CustomerController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/customers', [CustomerController::class, 'store'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/customers/{id}/delete', [CustomerController::class, 'softDelete'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/customers/{id}/restore', [CustomerController::class, 'restore'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::get('/repairs', [RepairController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/repairs', [RepairController::class, 'store'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/repairs/settings', [RepairController::class, 'settingsIndex'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::post('/repairs/settings', [RepairController::class, 'settingsCreate'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/repairs/settings/{id}', [RepairController::class, 'settingsUpdate'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::delete('/repairs/settings/{id}', [RepairController::class, 'settingsDelete'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::get('/repairs/{id}', [RepairController::class, 'show'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/repairs/{id}', [RepairController::class, 'update'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/repairs/{id}/assign-customer/{customer}', [RepairController::class, 'assignCustomer'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/repairs/{id}/delete', [RepairController::class, 'softDelete'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/repairs/{id}/restore', [RepairController::class, 'restore'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/repairs/{id}', [RepairController::class, 'destroy'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::get('/repairs/{id}/print', [RepairController::class, 'printReceipt'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/repairs/{id}/mail', [RepairController::class, 'mailReceipt'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/repairs/{id}/items', [RepairController::class, 'createItem'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/repairs/items/{item}', [RepairController::class, 'deleteItem'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/inventory/categories', [InventoryController::class, 'categoriesIndex'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/inventory/categories', [InventoryController::class, 'categoriesCreate'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/inventory/categories/{id}', [InventoryController::class, 'categoriesUpdate'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/inventory/categories/{id}', [InventoryController::class, 'categoriesDelete'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::get('/inventory/products', [InventoryController::class, 'productsIndex'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/inventory/products', [InventoryController::class, 'productsCreate'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/inventory/products/{id}', [InventoryController::class, 'productsShow'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/inventory/products/{id}', [InventoryController::class, 'productsUpdate'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/inventory/products/{id}', [InventoryController::class, 'productsDelete'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::post('/inventory/products/{id}/restock', [InventoryController::class, 'restock'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/inventory/products/{id}/quick-sell', [InventoryController::class, 'quickSell'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/inventory/transactions', [InventoryController::class, 'transactionsIndex'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::get('/inventory/transactions/{id}', [InventoryController::class, 'transactionsShow'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/inventory/transactions/{id}', [InventoryController::class, 'transactionsUpdate'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/inventory/transactions/{id}', [InventoryController::class, 'transactionsDelete'])->middleware(['auth:sanctum', 'api.locale']);

    Route::post('/inventory/repairs/{repair}/products/{product}/sell', [InventoryController::class, 'sellOnRepair'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/inventory/invoices/{invoice}/products/{product}/sell', [InventoryController::class, 'sellOnInvoice'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/inventory/{task}/{id}/transactions/{transaction}', [InventoryController::class, 'cancelTransaction'])->middleware(['auth:sanctum', 'api.locale']);

    Route::post('/barcode', [BarcodeController::class, 'scan'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/barcode/invoice', [BarcodeController::class, 'scanInvoice'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/barcode/repair', [BarcodeController::class, 'scanRepair'])->middleware(['auth:sanctum', 'api.locale']);

    Route::post('/invoices/items/create-repair/{repair}/{invoice}', [InvoiceController::class, 'createRepairItem'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/invoices', [InvoiceController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/invoices', [InvoiceController::class, 'create'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/invoices/{id}/delete', [InvoiceController::class, 'softDelete'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/invoices/{id}/restore', [InvoiceController::class, 'restore'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/invoices/{id}/update-customer/{customer}', [InvoiceController::class, 'updateCustomer'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/invoices/{id}/print/{task}', [InvoiceController::class, 'print'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/invoices/{id}/email', [InvoiceController::class, 'email'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/invoices/{id}/items', [InvoiceController::class, 'itemsIndex'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/invoices/{id}/items', [InvoiceController::class, 'createItem'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/invoices/{invoiceId}/items/{itemId}', [InvoiceController::class, 'updateItem'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/invoices/{invoiceId}/items/{itemId}', [InvoiceController::class, 'deleteItem'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/invoice-settings', [InvoiceController::class, 'settingsIndex'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::post('/invoice-settings', [InvoiceController::class, 'createSetting'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/invoice-settings/tax', [InvoiceController::class, 'updateTaxSetting'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/invoice-settings/{id}', [InvoiceController::class, 'updateSetting'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::delete('/invoice-settings/{id}', [InvoiceController::class, 'deleteSetting'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::get('/payments', [PaymentController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/payments/{id?}', [PaymentController::class, 'store'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/payments/{id}', [PaymentController::class, 'update'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::post('/reports/preview', [ReportController::class, 'preview'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/reports/print', [ReportController::class, 'print'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/reports/register', [ReportController::class, 'register'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/reports/register/insert', [ReportController::class, 'registerInsert'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/logs', [LogController::class, 'index'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::get('/trash', [TrashController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/users/profile', [UserController::class, 'profile'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/users/profile/password', [UserController::class, 'updatePassword'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/users', [UserController::class, 'index'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/users/{id}/password', [UserController::class, 'resetPassword'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::get('/settings', [SettingController::class, 'index'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/settings/business-profile', [SettingController::class, 'updateProfile'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);
    Route::put('/settings/{id}', [SettingController::class, 'update'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::post('/public-new-customer', [CustomerController::class, 'publicNewCustomer'])->middleware('api.locale');
});
