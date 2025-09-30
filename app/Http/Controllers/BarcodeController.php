<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;

use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\Log;
use App\Models\Payment;
use App\Models\Repair;

class BarcodeController extends Controller
{
    public function invoice_barcode(request $request)
    {
  
        $invoice = Invoice::where('id',$request->invoice)->first(); 
        $product = InventoryProduct::where('barcode',$request->barcode)->first();


        if($product){

            $purchases = InventoryTransaction::where('product_id',$product->id)->where('transaction','purchase')->sum('quantity');
            $sells = InventoryTransaction::where('product_id',$product->id)->where('transaction','sell')->sum('quantity');
            $stock = $purchases - $sells;

            if($stock > 0 ){

                /* SELL TRANSACTION */
                
                $check_transaction = InventoryTransaction::where('product_id',$product->id)->where('invoice_id',$invoice->id)->first();
        
                if($check_transaction){
                    $inventory_transaction = $check_transaction;
                    $inventory_transaction->quantity = $inventory_transaction ->quantity + 1;
                    $inventory_transaction->save();
                }else{
                    $inventory_transaction = new InventoryTransaction();
                    $inventory_transaction->product_id = $product->id;
                    $inventory_transaction->invoice_id = $invoice->id;
                    $inventory_transaction->transaction = 'sell';
                    $inventory_transaction->selling_price = $product->selling_price;
                    $inventory_transaction->quantity = 1;
                    $inventory_transaction->save();
                }
        
                $transactions_sum = 0;
                $transactions = InventoryTransaction::where('invoice_id',$invoice->id)->get();
                foreach($transactions as $transaction){
                    $transactions_sum = $transactions_sum + ($transaction->selling_price * $transaction->quantity);
                }
        
                $items_sum = InvoiceItem::where('invoice',$invoice->id)->sum('total') + $transactions_sum;
                $payments_sum = Payment::where('invoice',$invoice->id)->sum('amount');
        
                $invoice->subtotal = (float)$items_sum;
                $invoice->tax = (float)($items_sum / 100) *  (float)$invoice->tax_porcentage;
                $invoice->total = (float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage);
                $invoice->balance = ((float)$items_sum + (($items_sum / 100) *  (float)$invoice->tax_porcentage)) - $payments_sum;
                $invoice->save();
           
                $log = new Log; 
                $log->table = 'inventory_transactions';
                $log->data = 'Inventory Transaction has been Created';
                $log->ref = $inventory_transaction->id;
                $log->user = Auth::user()->id;
                $log->save();

                $response = 'new-transaction-created';
        
                
                /* SELL TRANSACTION */

            }else{
                $response = 'product-out-stock';
            }



        }else{
            $response = 'barcode-not-found';
        }

        return compact('response');

    }

    public function repair_barcode(request $request)
    {
  
        $repair = Repair::where('id',$request->repair)->first(); 
        $product = InventoryProduct::where('barcode',$request->barcode)->first();


        if($product){

            $purchases = InventoryTransaction::where('product_id',$product->id)->where('transaction','purchase')->sum('quantity');
            $sells = InventoryTransaction::where('product_id',$product->id)->where('transaction','sell')->sum('quantity');
            $stock = $purchases - $sells;

            if($stock > 0 ){

                /* SELL TRANSACTION */
                
                $check_transaction = InventoryTransaction::where('product_id',$product->id)->where('repair_id',$repair->id)->first();
        
                if($check_transaction){
                    $inventory_transaction = $check_transaction;
                    $inventory_transaction->quantity = $inventory_transaction ->quantity + 1;
                    $inventory_transaction->save();
                }else{
                    $inventory_transaction = new InventoryTransaction();
                    $inventory_transaction->product_id = $product->id;
                    $inventory_transaction->repair_id = $repair->id;
                    $inventory_transaction->transaction = 'sell';
                    $inventory_transaction->selling_price = $product->selling_price;
                    $inventory_transaction->quantity = 1;
                    $inventory_transaction->save();
                }
        
                $log = new Log; 
                $log->table = 'inventory_transactions';
                $log->data = 'Inventory Transaction has been Created';
                $log->ref = $inventory_transaction->id;
                $log->user = Auth::user()->id;
                $log->save();

                $response = 'new-transaction-created';
        
                
                /* SELL TRANSACTION */

            }else{
                $response = 'part-out-stock';
            }

        }else{
            $response = 'barcode-not-found';
        }

        return compact('response');

    }


    public function barcode(request $request)
    {
        $barcode = $request->barcode;

        $product = InventoryProduct::where('barcode',$barcode)->first();

        if($product){

            $purchases = InventoryTransaction::where('product_id',$product->id)->where('transaction','purchase')->sum('quantity');
            $sells = InventoryTransaction::where('product_id',$product->id)->where('transaction','sell')->sum('quantity');
            $stock = $purchases - $sells;

            $data_response = $stock;
            $response = 'product-found';
            $data = $product;

        }else{
            if(str_starts_with($barcode, 'INV')){
                $invoice_id = str_replace('INV', '', $barcode);
                $invoice = Invoice::where('id',$invoice_id)->first();

                if($invoice){
                    $data = $invoice;
                    $data_response = null;
                    $response = 'invoice-found';
                }else{
                    $data_response = $barcode;
                    $response = 'barcode-not-found';
                    $data = null;
                }
            }elseif(str_starts_with($barcode, 'REP')){
                $repair_id = str_replace('REP', '', $barcode);
                $repair = Repair::where('id',$repair_id)->first();

                if($repair){
                    $data = $repair;
                    $response = 'repair-found';
                    $data_response = null;
                }else{
                    $data_response = $barcode;
                    $response = 'barcode-not-found';
                    $data = null;
                }
            }else{
                
                $data_response = $barcode;
                $response = 'barcode-not-found';
                $data = null;
            }
        }

        return compact('response','data','data_response');

    }
}

