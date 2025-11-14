<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\AppController;


/* PUBLIC ROUTES */ 
Route::get('/', [PublicController::class, 'welcome'])->name('welcome')->middleware('locale');
Route::get('/customer-signup', [PublicController::class, 'customer_signup'])->name('customer-signup')->middleware('locale');
Route::post('/public-new-customer', [PublicController::class, 'public_new_customer'])->name('public-new-customer')->middleware('locale');

/* PRIVATE ROUTES */
Auth::routes(['verify' => true]);
Route::get('/home', [AdminController::class, 'index'])->name('home')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard')->middleware('verified')->middleware('notification')->middleware('locale');  

/* TRASH ROUTES */
Route::get('/index-trash', [TrashController::class, 'index_trash'])->name('index-trash')->middleware('verified')->middleware('notification')->middleware('locale');  
Route::post('/index-trash', [TrashController::class, 'index_trash'])->name('search-trash')->middleware('verified')->middleware('notification')->middleware('locale');   

/* SETTING ROUTES */
Route::get('/index-setting', [SettingController::class, 'index_setting'])->name('index-setting')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');       
Route::put('/update-setting/{id}', [SettingController::class, 'update_setting'])->name('update-setting')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');  
Route::put('/update-company-setting/{id}', [SettingController::class, 'update_company_setting'])->name('update-company-setting')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   

/* LOG ROUTES */
Route::get('/index-log', [LogController::class, 'index_log'])->name('index-log')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');  
Route::post('/index-log', [LogController::class, 'index_log'])->name('search-log')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   

/* PAYMENT ROUTES */
Route::get('/new-payment', [PaymentController::class, 'new_payment'])->name('new-payment')->middleware('verified')->middleware('notification')->middleware('locale');  
Route::post('/create-payment/{id?}', [PaymentController::class, 'create_payment'])->name('create-payment')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::delete('/delete-payment/{id}', [PaymentController::class, 'delete_payment'])->name('delete-payment')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');
Route::get('/index-payment', [PaymentController::class, 'index_payment'])->name('index-payment')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/index-payment', [PaymentController::class, 'index_payment'])->name('search-payment')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/view-payment/{id}', [PaymentController::class, 'view_payment'])->name('view-payment')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/update-payment/{id}', [PaymentController::class, 'update_payment'])->name('update-payment')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/delete-payment/{id}', [PaymentController::class, 'delete_payment'])->name('delete-payment')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   

/* NOTIFICATION ROUTES */
Route::get('/notification/{id}', [NotificationController::class, 'notification'])->name('notification')->middleware('verified')->middleware('notification')->middleware('locale');  

/* CUSTOMER ROUTES */
Route::get('/index-customer/{task?}/{id?}', [CustomerController::class, 'index_customer'])->name('index-customer')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/index-customer/{task?}/{id?}', [CustomerController::class, 'index_customer'])->name('search-customer')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/create-customer', [CustomerController::class, 'create_customer'])->name('create-customer')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::post('/new-customer', [CustomerController::class, 'new_customer'])->name('new-customer')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/view-customer/{id}', [CustomerController::class, 'view_customer'])->name('view-customer')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/update-customer/{id}', [CustomerController::class, 'update_customer'])->name('update-customer')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/delete-customer/{id}', [CustomerController::class, 'delete_customer'])->name('delete-customer')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/restore-customer/{id}', [CustomerController::class, 'restore_customer'])->name('restore-customer')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::delete('/destroy-customer/{id}', [CustomerController::class, 'destroy_customer'])->name('destroy-customer')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    

/* REPAIR ROUTES */
Route::get('/index-repair/{task?}/{id?}', [RepairController::class, 'index_repair'])->name('index-repair')->middleware('verified')->middleware('notification')->middleware('locale'); 
Route::post('/index-repair/{task?}/{id?}', [RepairController::class, 'index_repair'])->name('search-repair')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/create-repair/{id?}', [RepairController::class, 'create_repair'])->name('create-repair')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/new-repair/{id?}', [RepairController::class, 'new_repair'])->name('new-repair')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/view-repair/{id}', [RepairController::class, 'view_repair'])->name('view-repair')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/update-repair/{id}', [RepairController::class, 'update_repair'])->name('update-repair')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/delete-repair/{id}', [RepairController::class, 'delete_repair'])->name('delete-repair')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/restore-repair/{id}', [RepairController::class, 'restore_repair'])->name('restore-repair')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::delete('/destroy-repair/{id}', [RepairController::class, 'destroy_repair'])->name('destroy-repair')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('print-repair/{id}', [RepairController::class, 'print_repair'])->name('print-repair')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('mail-repair/{id}', [RepairController::class, 'mail_repair'])->name('mail-repair')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/update-customer-repair/{customer}/{repair}', [RepairController::class, 'update_customer_repair'])->name('update-customer-repair')->middleware('verified')->middleware('notification')->middleware('locale');    

/* REPAIR SETTINGS ROUTES */
Route::get('/setting-repair', [RepairController::class, 'setting_repair'])->name('setting-repair')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');  
Route::post('/setting-repair/create', [RepairController::class, 'create_setting_repair'])->name('create-setting-repair')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/setting-repair/update/{id}', [RepairController::class, 'update_setting_repair'])->name('update-setting-repair')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale'); 
Route::delete('/setting-repair/delete/{id}', [RepairController::class, 'delete_setting_repair'])->name('delete-setting-repair')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   

/* REPAIR ITEMS ROUTES */
Route::post('/item-repair/create/{id}', [RepairController::class, 'create_item_repair'])->name('create-item-repair')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::delete('/item-repair/delete/{id}', [RepairController::class, 'delete_item_repair'])->name('delete-item-repair')->middleware('verified')->middleware('notification')->middleware('locale');   

/* INVOICE ROUTES */
Route::get('/index-invoice/{task?}', [InvoiceController::class, 'index_invoice'])->name('index-invoice')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/index-invoice/{task?}', [InvoiceController::class, 'index_invoice'])->name('search-invoice')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::get('/view-invoice/{id}', [InvoiceController::class, 'view_invoice'])->name('view-invoice')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/create-invoice/{id}/{task}', [InvoiceController::class, 'create_invoice'])->name('create-invoice')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/update-invoice/{id}', [InvoiceController::class, 'update_invoice'])->name('update-invoice')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/print-invoice/{id}/{task}', [InvoiceController::class, 'print_invoice'])->name('print-invoice')->middleware('verified')->middleware('notification')->middleware('locale');
Route::get('/email-invoice/{id}', [InvoiceController::class, 'email_invoice'])->name('email-invoice')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/delete-invoice/{id}', [InvoiceController::class, 'delete_invoice'])->name('delete-invoice')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/restore-invoice/{id}', [InvoiceController::class, 'restore_invoice'])->name('restore-invoice')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::delete('/destroy-invoice/{id}', [InvoiceController::class, 'destroy_invoice'])->name('destroy-invoice')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/update-customer-invoice/{customer}/{repair}', [InvoiceController::class, 'update_customer_invoice'])->name('update-customer-invoice')->middleware('verified')->middleware('notification')->middleware('locale');   

/* INVOICE ITEMS ROUTES */
Route::post('/item-invoice/create/{id}', [InvoiceController::class, 'create_item_invoice'])->name('create-item-invoice')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::post('/item-invoice/create-repair/{repair}/{invoice}', [InvoiceController::class, 'create_item_repair_invoice'])->name('create-item-repair-invoice')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/item-invoice/update/{id}', [InvoiceController::class, 'update_item_invoice'])->name('update-item-invoice')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::delete('/item-invoice/delete/{id}', [InvoiceController::class, 'delete_item_invoice'])->name('delete-item-invoice')->middleware('verified')->middleware('notification')->middleware('locale');   

/* INVOICE SETTINGS ROUTES */
Route::get('/setting-invoice', [InvoiceController::class, 'setting_invoice'])->name('setting-invoice')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::post('/setting-invoice/create', [InvoiceController::class, 'create_setting_invoice'])->name('create-setting-invoice')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/setting-invoice/update/{id}', [InvoiceController::class, 'update_setting_invoice'])->name('update-setting-invoice')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::delete('/setting-invoice/delete/{id}', [InvoiceController::class, 'delete_setting_invoice'])->name('delete-setting-invoice')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    

/* REPORTS ROUTES */
Route::get('/create-report', [ReportController::class, 'create_report'])->name('create-report')->middleware('notification')->middleware('locale');    
Route::get('/create-register-report', [ReportController::class, 'create_register_report'])->name('create-register-report')->middleware('notification')->middleware('locale');    
Route::post('/get-report', [ReportController::class, 'get_report'])->name('get-report')->middleware('notification')->middleware('locale');
Route::post('/get-register-report', [ReportController::class, 'get_register_report'])->name('get-register-report')->middleware('notification')->middleware('locale');    
Route::get('/print-report/{report_from}/{report_to}/{report_invoices}/{report_repairs}/{report_payment}', [ReportController::class, 'print_report'])->name('print-report')->middleware('notification')->middleware('locale');    
Route::get('/print-register-report', [ReportController::class, 'print_register_report'])->name('print-register-report')->middleware('notification')->middleware('locale');   
Route::post('/register-report/insert-data', [ReportController::class, 'register_report_insert'])->name('register-report-insert')->middleware('notification')->middleware('locale');    

/* USERS ROUTES */
Route::get('/profile', [UserController::class, 'profile'])->name('profile')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/profile/update-password', [UserController::class, 'update_password'])->name('profile-update-password')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::get('/users', [UserController::class, 'users'])->name('users')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/users/update-user/{id}', [UserController::class, 'update_user'])->name('update-user')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::delete('/users/delete/{id}', [UserController::class, 'delete_user'])->name('delete-user')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    

/* INVENTORY ROUTES */
Route::get('/inventory/categories/index', [InventoryController::class, 'inventory_index_category'])->name('inventory-index-category')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::post('/inventory/categories/create', [InventoryController::class, 'inventory_create_category'])->name('inventory-create-category')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/inventory/category/update/{id}', [InventoryController::class, 'inventory_update_category'])->name('inventory-update-category')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::delete('/inventory/category/delete/{id}', [InventoryController::class, 'inventory_delete_category'])->name('inventory-delete-category')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');    

Route::get('/inventory/transactions/index', [InventoryController::class, 'inventory_index_transaction'])->name('inventory-index-transaction')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::post('/inventory/transactions/index', [InventoryController::class, 'inventory_index_transaction'])->name('inventory-search-transaction')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::get('/inventory/transaction/view/{id}', [InventoryController::class, 'inventory_view_transaction'])->name('inventory-view-transaction')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::get('/inventory/transaction/restock/{id}', [InventoryController::class, 'inventory_restock_transaction'])->name('inventory-restock-transaction')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::post('/inventory/transaction/sell/{task}/{id}/{product_id}', [InventoryController::class, 'inventory_sell_transaction'])->name('inventory-sell-transaction')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::delete('/inventory/transaction/cancel/{task}/{id}/{transaction}', [InventoryController::class, 'inventory_cancel_transaction'])->name('inventory-cancel-transaction')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/inventory/transaction/create/{id}', [InventoryController::class, 'inventory_create_transaction'])->name('inventory-create-transaction')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::put('/inventory/transaction/update/{id}', [InventoryController::class, 'inventory_update_transaction'])->name('inventory-update-transaction')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::delete('/inventory/transaction/delete/{id}', [InventoryController::class, 'inventory_delete_transaction'])->name('inventory-delete-transaction')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/inventory/transaction/quick-sell/{id}', [InventoryController::class, 'inventory_quick_sell_transaction'])->name('inventory-quick-sell-transaction')->middleware('verified')->middleware('notification')->middleware('locale');    

Route::get('/inventory/products/{task?}/{id?}', [InventoryController::class, 'inventory_index_product'])->name('inventory-index-product')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::post('/inventory/products/{task?}/{id?}', [InventoryController::class, 'inventory_index_product'])->name('inventory-search-product')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::get('/inventory/product/view/{id}', [InventoryController::class, 'inventory_view_product'])->name('inventory-view-product')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/inventory/product/create', [InventoryController::class, 'inventory_create_product'])->name('inventory-create-product')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::put('/inventory/product/update/{id}', [InventoryController::class, 'inventory_update_product'])->name('inventory-update-product')->middleware('verified')->middleware('notification')->middleware('locale');   
Route::delete('/inventory/product/delete/{id}', [InventoryController::class, 'inventory_delete_product'])->name('inventory-delete-product')->middleware('admin')->middleware('verified')->middleware('notification')->middleware('locale');   

/* BARCODE ROUTES */
Route::post('/barcode', [BarcodeController::class, 'barcode'])->name('barcode')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/barcode/invoice', [BarcodeController::class, 'invoice_barcode'])->name('invoice-barcode')->middleware('verified')->middleware('notification')->middleware('locale');    
Route::post('/barcode/repair', [BarcodeController::class, 'repair_barcode'])->name('repair-barcode')->middleware('verified')->middleware('notification')->middleware('locale');

/* APP ROUTES */
Route::get('/app/session/set-sidebar-position/{position}', [AppController::class, 'set_sidebar_position'])->name('set-sidebar-position')->middleware('notification')->middleware('locale');   

