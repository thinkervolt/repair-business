<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\RepairController;

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

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware(['auth:sanctum', 'api.admin']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/auth/email/verify', [AuthController::class, 'verifyEmail'])->middleware('api.locale');
    Route::post('/auth/email/resend', [AuthController::class, 'resendVerification'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/auth/password/email', [AuthController::class, 'forgotPassword'])->middleware('api.locale');
    Route::post('/auth/password/reset', [AuthController::class, 'resetPassword'])->middleware('api.locale');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/notifications', [NotificationController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);

    Route::get('/customers', [CustomerController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/customers', [CustomerController::class, 'store'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/customers/{id}/delete', [CustomerController::class, 'softDelete'])->middleware(['auth:sanctum', 'api.locale']);
    Route::put('/customers/{id}/restore', [CustomerController::class, 'restore'])->middleware(['auth:sanctum', 'api.locale']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware(['auth:sanctum', 'api.admin', 'api.locale']);

    Route::get('/repairs', [RepairController::class, 'index'])->middleware(['auth:sanctum', 'api.locale']);
    Route::post('/repairs', [RepairController::class, 'store'])->middleware(['auth:sanctum', 'api.locale']);
    Route::get('/repairs/settings', [RepairController::class, 'settingsIndex'])->middleware(['auth:sanctum', 'api.locale']);
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

    Route::post('/public-new-customer', [CustomerController::class, 'publicNewCustomer'])->middleware('api.locale');
});