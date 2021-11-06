<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', 'AdminController@index')->name('home')->middleware('verified');
Route::get('/dashboard', 'AdminController@index')->name('dashboard')->middleware('verified');
Route::get('/customer-signup', 'PublicController@customer_signup')->name('customer-signup');
Route::post('/public-new-customer', 'PublicController@public_new_customer')->name('public-new-customer');

/* TRASH ROUTES */
Route::get('/index-trash', 'TrashController@index_trash')->name('index-trash')->middleware('verified');
Route::post('/index-trash', 'TrashController@index_trash')->name('search-trash')->middleware('verified');

/* SETTING ROUTES */
Route::get('/index-setting', 'SettingController@index_setting')->name('index-setting')->middleware('admin')->middleware('verified');
Route::put('/update-setting/{id}', 'SettingController@update_setting')->name('update-setting')->middleware('admin')->middleware('verified');
Route::put('/update-company-setting/{id}', 'SettingController@update_company_setting')->name('update-company-setting')->middleware('admin')->middleware('verified');

/* LOG ROUTES */
Route::get('/index-log', 'LogController@index_log')->name('index-log')->middleware('admin')->middleware('verified');
Route::post('/index-log', 'LogController@index_log')->name('search-log')->middleware('admin')->middleware('verified');

/* PAYMENT ROUTES */
Route::get('/new-payment', 'PaymentController@new_payment')->name('new-payment')->middleware('verified');
Route::post('/create-payment/{id?}', 'PaymentController@create_payment')->name('create-payment')->middleware('verified');
Route::delete('/delete-payment/{id}', 'PaymentController@delete_payment')->name('delete-payment')->middleware('admin')->middleware('verified');
Route::get('/index-payment', 'PaymentController@index_payment')->name('index-payment')->middleware('verified');
Route::post('/index-payment', 'PaymentController@index_payment')->name('search-payment')->middleware('verified');
Route::get('/view-payment/{id}', 'PaymentController@view_payment')->name('view-payment')->middleware('verified');;
Route::put('/update-payment/{id}', 'PaymentController@update_payment')->name('update-payment')->middleware('admin')->middleware('verified');
Route::put('/delete-payment/{id}', 'PaymentController@delete_payment')->name('delete-payment')->middleware('admin')->middleware('verified');

/* NOTIFICATION ROUTES */
Route::get('/notification/{id}', 'NotificationController@notification')->name('notification')->middleware('verified');

/* CUSTOMER ROUTES */
Route::get('/index-customer/{task?}/{id?}', 'CustomerController@index_customer')->name('index-customer')->middleware('verified');
Route::post('/index-customer/{task?}/{id?}', 'CustomerController@index_customer')->name('search-customer')->middleware('verified');
Route::get('/create-customer', 'CustomerController@create_customer')->name('create-customer')->middleware('verified');
Route::post('/new-customer', 'CustomerController@new_customer')->name('new-customer')->middleware('verified');
Route::get('/view-customer/{id}', 'CustomerController@view_customer')->name('view-customer')->middleware('verified');
Route::put('/update-customer/{id}', 'CustomerController@update_customer')->name('update-customer')->middleware('verified');
Route::put('/delete-customer/{id}', 'CustomerController@delete_customer')->name('delete-customer')->middleware('verified');
Route::put('/restore-customer/{id}', 'CustomerController@restore_customer')->name('restore-customer')->middleware('verified');
Route::delete('/destroy-customer/{id}', 'CustomerController@destroy_customer')->name('destroy-customer')->middleware('admin')->middleware('verified');

/* REPAIR ROUTES */
Route::get('/index-repair/{task?}/{id?}', 'RepairController@index_repair')->name('index-repair')->middleware('verified');
Route::post('/index-repair/{task?}/{id?}', 'RepairController@index_repair')->name('search-repair')->middleware('verified');
Route::get('/create-repair/{id?}', 'RepairController@create_repair')->name('create-repair')->middleware('verified');
Route::post('/new-repair/{id?}', 'RepairController@new_repair')->name('new-repair')->middleware('verified');
Route::get('/view-repair/{id}', 'RepairController@view_repair')->name('view-repair')->middleware('verified');
Route::put('/update-repair/{id}', 'RepairController@update_repair')->name('update-repair')->middleware('verified');
Route::put('/delete-repair/{id}', 'RepairController@delete_repair')->name('delete-repair')->middleware('verified');
Route::put('/restore-repair/{id}', 'RepairController@restore_repair')->name('restore-repair')->middleware('verified');
Route::delete('/destroy-repair/{id}', 'RepairController@destroy_repair')->name('destroy-repair')->middleware('admin')->middleware('verified');
Route::get('print-repair/{id}', 'RepairController@print_repair')->name('print-repair')->middleware('verified');
Route::put('/update-customer-repair/{customer}/{repair}', 'RepairController@update_customer_repair')->name('update-customer-repair')->middleware('verified');

/* REPAIR SETTINGS ROUTES */
Route::get('/setting-repair', 'RepairController@setting_repair')->name('setting-repair')->middleware('admin')->middleware('verified');
Route::post('/setting-repair/create', 'RepairController@create_setting_repair')->name('create-setting-repair')->middleware('admin')->middleware('verified');
Route::put('/setting-repair/update/{id}', 'RepairController@update_setting_repair')->name('update-setting-repair')->middleware('admin')->middleware('verified');
Route::delete('/setting-repair/delete/{id}', 'RepairController@delete_setting_repair')->name('delete-setting-repair')->middleware('admin')->middleware('verified');

/* REPAIR ITEMS ROUTES */
Route::post('/item-repair/create/{id}', 'RepairController@create_item_repair')->name('create-item-repair')->middleware('verified');
Route::delete('/item-repair/delete/{id}', 'RepairController@delete_item_repair')->name('delete-item-repair')->middleware('verified');

/* INVOICE ROUTES */
Route::get('/index-invoice/{task?}', 'InvoiceController@index_invoice')->name('index-invoice')->middleware('verified');
Route::post('/index-invoice/{task?}', 'InvoiceController@index_invoice')->name('search-invoice')->middleware('verified');
Route::get('/view-invoice/{id}', 'InvoiceController@view_invoice')->name('view-invoice')->middleware('verified');
Route::post('/create-invoice/{id}/{task}', 'InvoiceController@create_invoice')->name('create-invoice')->middleware('verified');
Route::put('/update-invoice/{id}', 'InvoiceController@update_invoice')->name('update-invoice')->middleware('verified');
Route::get('/print-invoice/{id}/{task}', 'InvoiceController@print_invoice')->name('print-invoice')->middleware('verified');
Route::put('/delete-invoice/{id}', 'InvoiceController@delete_invoice')->name('delete-invoice')->middleware('verified');
Route::put('/restore-invoice/{id}', 'InvoiceController@restore_invoice')->name('restore-invoice')->middleware('verified');
Route::delete('/destroy-invoice/{id}', 'InvoiceController@destroy_invoice')->name('destroy-invoice')->middleware('admin')->middleware('verified');
Route::put('/update-customer-invoice/{customer}/{repair}', 'InvoiceController@update_customer_invoice')->name('update-customer-invoice')->middleware('verified');

/* INVOICE ITEMS ROUTES */
Route::post('/item-invoice/create/{id}', 'InvoiceController@create_item_invoice')->name('create-item-invoice')->middleware('verified');
Route::post('/item-invoice/create-repair/{repair}/{invoice}', 'InvoiceController@create_item_repair_invoice')->name('create-item-repair-invoice')->middleware('verified');
Route::put('/item-invoice/update/{id}', 'InvoiceController@update_item_invoice')->name('update-item-invoice')->middleware('verified');
Route::delete('/item-invoice/delete/{id}', 'InvoiceController@delete_item_invoice')->name('delete-item-invoice')->middleware('verified');

/* INVOICE SETTINGS ROUTES */
Route::get('/setting-invoice', 'InvoiceController@setting_invoice')->name('setting-invoice')->middleware('admin')->middleware('verified');
Route::post('/setting-invoice/create', 'InvoiceController@create_setting_invoice')->name('create-setting-invoice')->middleware('admin')->middleware('verified');
Route::put('/setting-invoice/update/{id}', 'InvoiceController@update_setting_invoice')->name('update-setting-invoice')->middleware('admin')->middleware('verified');
Route::delete('/setting-invoice/delete/{id}', 'InvoiceController@delete_setting_invoice')->name('delete-setting-invoice')->middleware('admin')->middleware('verified');

/* REPORTS ROUTES */
Route::get('/create-report', 'ReportController@create_report')->name('create-report')->middleware('verified');
Route::post('/get-report', 'ReportController@get_report')->name('get-report')->middleware('verified');
Route::get('/print-report/{report_from}/{report_to}/{report_invoices}/{report_repairs}/{report_payment}', 'ReportController@print_report')->name('print-report')->middleware('verified');

/* USERS ROUTES */
Route::get('/profile', 'UserController@profile')->name('profile')->middleware('verified');
Route::put('/profile/update-password', 'UserController@update_password')->name('profile-update-password')->middleware('verified');
Route::get('/users', 'UserController@users')->name('users')->middleware('admin')->middleware('verified');
Route::put('/users/update-user/{id}', 'UserController@update_user')->name('update-user')->middleware('admin')->middleware('verified');
Route::delete('/users/delete/{id}', 'UserController@delete_user')->name('delete-user')->middleware('admin')->middleware('verified');

/* INVENTORY ROUTES */
Route::get('/inventory/categories/index', 'InventoryController@inventory_index_category')->name('inventory-index-category')->middleware('verified');
Route::post('/inventory/categories/create', 'InventoryController@inventory_create_category')->name('inventory-create-category')->middleware('verified');
Route::put('/inventory/category/update/{id}', 'InventoryController@inventory_update_category')->name('inventory-update-category')->middleware('verified');
Route::delete('/inventory/category/delete/{id}', 'InventoryController@inventory_delete_category')->name('inventory-delete-category')->middleware('admin')->middleware('verified');

Route::get('/inventory/transactions/index', 'InventoryController@inventory_index_transaction')->name('inventory-index-transaction')->middleware('admin')->middleware('verified');
Route::post('/inventory/transactions/index', 'InventoryController@inventory_index_transaction')->name('inventory-search-transaction')->middleware('admin')->middleware('verified');
Route::get('/inventory/transaction/view/{id}', 'InventoryController@inventory_view_transaction')->name('inventory-view-transaction')->middleware('admin')->middleware('verified');
Route::get('/inventory/transaction/restock/{id}', 'InventoryController@inventory_restock_transaction')->name('inventory-restock-transaction')->middleware('verified');
Route::post('/inventory/transaction/sell/{task}/{id}/{product_id}', 'InventoryController@inventory_sell_transaction')->name('inventory-sell-transaction')->middleware('verified');
Route::delete('/inventory/transaction/cancel/{task}/{id}/{transaction}', 'InventoryController@inventory_cancel_transaction')->name('inventory-cancel-transaction')->middleware('verified');
Route::post('/inventory/transaction/create/{id}', 'InventoryController@inventory_create_transaction')->name('inventory-create-transaction')->middleware('verified');
Route::put('/inventory/transaction/update/{id}', 'InventoryController@inventory_update_transaction')->name('inventory-update-transaction')->middleware('verified');
Route::delete('/inventory/transaction/delete/{id}', 'InventoryController@inventory_delete_transaction')->name('inventory-delete-transaction')->middleware('verified');
Route::get('/inventory/transaction/quick-sell/{id}', 'InventoryController@inventory_quick_sell_transaction')->name('inventory-quick-sell-transaction')->middleware('verified');

Route::get('/inventory/products/{task?}/{id?}', 'InventoryController@inventory_index_product')->name('inventory-index-product')->middleware('verified');
Route::post('/inventory/products/{task?}/{id?}', 'InventoryController@inventory_index_product')->name('inventory-search-product')->middleware('verified');
Route::get('/inventory/product/view/{id}', 'InventoryController@inventory_view_product')->name('inventory-view-product')->middleware('verified');
Route::post('/inventory/product/create', 'InventoryController@inventory_create_product')->name('inventory-create-product')->middleware('verified');
Route::put('/inventory/product/update/{id}', 'InventoryController@inventory_update_product')->name('inventory-update-product')->middleware('verified');
Route::delete('/inventory/product/delete/{id}', 'InventoryController@inventory_delete_product')->name('inventory-delete-product')->middleware('admin')->middleware('verified');

/* BARCODE ROUTES */

Route::post('/barcode', 'BarcodeController@barcode')->name('barcode')->middleware('verified');
Route::post('/barcode/invoice', 'BarcodeController@invoice_barcode')->name('invoice-barcode')->middleware('verified');
Route::post('/barcode/repair', 'BarcodeController@repair_barcode')->name('repair-barcode')->middleware('verified');