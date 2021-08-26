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

Auth::routes();

Route::get('/home', 'AdminController@index')->name('home');
Route::get('/dashboard', 'AdminController@index')->name('dashboard');
Route::get('/customer-signup', 'PublicController@customer_signup')->name('customer-signup');
Route::post('/public-new-customer', 'PublicController@public_new_customer')->name('public-new-customer');

/* TRASH ROUTES */
Route::get('/index-trash', 'TrashController@index_trash')->name('index-trash');
Route::post('/index-trash', 'TrashController@index_trash')->name('search-trash');

/* SETTING ROUTES */
Route::get('/index-setting', 'SettingController@index_setting')->name('index-setting')->middleware('admin');
Route::put('/update-setting/{id}', 'SettingController@update_setting')->name('update-setting')->middleware('admin');
Route::put('/update-company-setting/{id}', 'SettingController@update_company_setting')->name('update-company-setting')->middleware('admin');

/* LOG ROUTES */
Route::get('/index-log', 'LogController@index_log')->name('index-log')->middleware('admin');
Route::post('/index-log', 'LogController@index_log')->name('search-log')->middleware('admin');

/* PAYMENT ROUTES */
Route::get('/new-payment', 'PaymentController@new_payment')->name('new-payment');
Route::post('/create-payment/{id?}', 'PaymentController@create_payment')->name('create-payment');
Route::delete('/delete-payment/{id}', 'PaymentController@delete_payment')->name('delete-payment')->middleware('admin');
Route::get('/index-payment', 'PaymentController@index_payment')->name('index-payment');
Route::post('/index-payment', 'PaymentController@index_payment')->name('search-payment');
Route::get('/view-payment/{id}', 'PaymentController@view_payment')->name('view-payment');
Route::put('/update-payment/{id}', 'PaymentController@update_payment')->name('update-payment')->middleware('admin');
Route::put('/delete-payment/{id}', 'PaymentController@delete_payment')->name('delete-payment')->middleware('admin');

/* NOTIFICATION ROUTES */
Route::get('/notification/{id}', 'NotificationController@notification')->name('notification');

/* CUSTOMER ROUTES */
Route::get('/index-customer/{task?}/{id?}', 'CustomerController@index_customer')->name('index-customer');
Route::post('/index-customer/{task?}/{id?}', 'CustomerController@index_customer')->name('search-customer');
Route::get('/create-customer', 'CustomerController@create_customer')->name('create-customer');
Route::post('/new-customer', 'CustomerController@new_customer')->name('new-customer');
Route::get('/view-customer/{id}', 'CustomerController@view_customer')->name('view-customer');
Route::put('/update-customer/{id}', 'CustomerController@update_customer')->name('update-customer');
Route::put('/delete-customer/{id}', 'CustomerController@delete_customer')->name('delete-customer');
Route::put('/restore-customer/{id}', 'CustomerController@restore_customer')->name('restore-customer');
Route::delete('/destroy-customer/{id}', 'CustomerController@destroy_customer')->name('destroy-customer')->middleware('admin');

/* REPAIR ROUTES */
Route::get('/index-repair/{task?}/{id?}', 'RepairController@index_repair')->name('index-repair');
Route::post('/index-repair/{task?}/{id?}', 'RepairController@index_repair')->name('search-repair');
Route::get('/create-repair/{id?}', 'RepairController@create_repair')->name('create-repair');
Route::post('/new-repair/{id?}', 'RepairController@new_repair')->name('new-repair');
Route::get('/view-repair/{id}', 'RepairController@view_repair')->name('view-repair');
Route::put('/update-repair/{id}', 'RepairController@update_repair')->name('update-repair');
Route::put('/delete-repair/{id}', 'RepairController@delete_repair')->name('delete-repair');
Route::put('/restore-repair/{id}', 'RepairController@restore_repair')->name('restore-repair');
Route::delete('/destroy-repair/{id}', 'RepairController@destroy_repair')->name('destroy-repair')->middleware('admin');
Route::get('print-repair/{id}', 'RepairController@print_repair')->name('print-repair');
Route::put('/update-customer-repair/{customer}/{repair}', 'RepairController@update_customer_repair')->name('update-customer-repair');

/* REPAIR SETTINGS ROUTES */
Route::get('/setting-repair', 'RepairController@setting_repair')->name('setting-repair')->middleware('admin');
Route::post('/setting-repair/create', 'RepairController@create_setting_repair')->name('create-setting-repair')->middleware('admin');
Route::put('/setting-repair/update/{id}', 'RepairController@update_setting_repair')->name('update-setting-repair')->middleware('admin');
Route::delete('/setting-repair/delete/{id}', 'RepairController@delete_setting_repair')->name('delete-setting-repair')->middleware('admin');

/* REPAIR ITEMS ROUTES */
Route::post('/item-repair/create/{id}', 'RepairController@create_item_repair')->name('create-item-repair');
Route::delete('/item-repair/delete/{id}', 'RepairController@delete_item_repair')->name('delete-item-repair');

/* INVOICE ROUTES */
Route::get('/index-invoice/{task?}', 'InvoiceController@index_invoice')->name('index-invoice');
Route::post('/index-invoice/{task?}', 'InvoiceController@index_invoice')->name('search-invoice');
Route::get('/view-invoice/{id}', 'InvoiceController@view_invoice')->name('view-invoice');
Route::post('/create-invoice/{id}/{task}', 'InvoiceController@create_invoice')->name('create-invoice');
Route::put('/update-invoice/{id}', 'InvoiceController@update_invoice')->name('update-invoice');
Route::get('/print-invoice/{id}/{task}', 'InvoiceController@print_invoice')->name('print-invoice');
Route::put('/delete-invoice/{id}', 'InvoiceController@delete_invoice')->name('delete-invoice');
Route::put('/restore-invoice/{id}', 'InvoiceController@restore_invoice')->name('restore-invoice');
Route::delete('/destroy-invoice/{id}', 'InvoiceController@destroy_invoice')->name('destroy-invoice')->middleware('admin');
Route::put('/update-customer-invoice/{customer}/{repair}', 'InvoiceController@update_customer_invoice')->name('update-customer-invoice');

/* INVOICE ITEMS ROUTES */
Route::post('/item-invoice/create/{id}', 'InvoiceController@create_item_invoice')->name('create-item-invoice');
Route::post('/item-invoice/create-repair/{repair}/{invoice}', 'InvoiceController@create_item_repair_invoice')->name('create-item-repair-invoice');
Route::put('/item-invoice/update/{id}', 'InvoiceController@update_item_invoice')->name('update-item-invoice');
Route::delete('/item-invoice/delete/{id}', 'InvoiceController@delete_item_invoice')->name('delete-item-invoice');

/* INVOICE SETTINGS ROUTES */
Route::get('/setting-invoice', 'InvoiceController@setting_invoice')->name('setting-invoice')->middleware('admin');
Route::post('/setting-invoice/create', 'InvoiceController@create_setting_invoice')->name('create-setting-invoice')->middleware('admin');
Route::put('/setting-invoice/update/{id}', 'InvoiceController@update_setting_invoice')->name('update-setting-invoice')->middleware('admin');
Route::delete('/setting-invoice/delete/{id}', 'InvoiceController@delete_setting_invoice')->name('delete-setting-invoice')->middleware('admin');

/* REPORTS ROUTES */
Route::get('/create-report', 'ReportController@create_report')->name('create-report');
Route::get('/create-register-report', 'ReportController@create_register_report')->name('create-register-report');
Route::post('/get-report', 'ReportController@get_report')->name('get-report');
Route::post('/get-register-report', 'ReportController@get_register_report')->name('get-register-report');
Route::get('/print-report/{report_from}/{report_to}/{report_invoices}/{report_repairs}/{report_payment}', 'ReportController@print_report')->name('print-report');
Route::get('/print-register-report', 'ReportController@print_register_report')->name('print-register-report');
Route::post('/register-report/insert-data', 'ReportController@register_report_insert')->name('register-report-insert');

/* USERS ROUTES */
Route::get('/profile', 'UserController@profile')->name('profile');
Route::put('/profile/update-password', 'UserController@update_password')->name('profile-update-password');
Route::get('/users', 'UserController@users')->name('users')->middleware('admin');
Route::put('/users/update-user/{id}', 'UserController@update_user')->name('update-user')->middleware('admin');
Route::delete('/users/delete/{id}', 'UserController@delete_user')->name('delete-user')->middleware('admin');
