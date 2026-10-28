<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\MailInvoice;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceSetting;
use App\Models\Log;
use App\Models\Payment;
use App\Models\Repair;
use App\Models\RepairItem;
use App\Models\Setting;
use App\Traits\ApiResponses;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    use ApiResponses;

    protected function companyProfile()
    {
        $settings = Setting::where('group', 'business_profile')->get();
        $profile = (object)[];
        foreach ($settings as $setting) {
            $profile->{$setting->name} = $setting->data;
        }
        return $profile;
    }

    protected function taxSetting()
    {
        $setting = Setting::where('name', 'invoice_tax')->where('group', 'tax')->first();
        return $setting ? (float)$setting->data : 0;
    }

    protected function recomputeInvoice(Invoice $invoice)
    {
        $transactions_sum = 0;
        $transactions = InventoryTransaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            $transactions_sum += (float)($transaction->selling_price * $transaction->quantity);
        }

        $items_sum = (float)InvoiceItem::where('invoice', $invoice->id)->sum('total') + $transactions_sum;
        $payments_sum = (float)Payment::where('invoice', $invoice->id)->where('active', 'yes')->sum('amount');
        $tax_porcentage = (float)$invoice->tax_porcentage;


        $invoice->subtotal = (float)$items_sum;
        $invoice->tax = (float)($items_sum / 100) * $tax_porcentage;
        $invoice->total = (float)$items_sum + (($items_sum / 100) * $tax_porcentage);
        $invoice->balance = (float)$invoice->total - $payments_sum;
        $invoice->save();
    }

