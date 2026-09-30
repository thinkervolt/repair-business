<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class Notification extends Model
{
    protected $fillable = ['message', 'route', 'ref'];

    public static function createIfMissing($route, $ref, $message)
    {
        if (static::where('route', $route)->where('ref', $ref)->exists()) {
            return false;
        }

        $notification = new static;
        $notification->message = $message;
        $notification->route = $route;
        $notification->ref = (int)$ref;
        $notification->save();

        return true;
    }

    public static function clearFor($route, $ref)
    {
        static::where('route', $route)->where('ref', (int)$ref)->delete();
    }

    public static function syncProductStock(InventoryProduct $product)
    {
        $purchases = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'purchase')->sum('quantity');
        $sells = InventoryTransaction::where('product_id', $product->id)->where('transaction', 'sell')->sum('quantity');
        $stock = (int)$purchases - (int)$sells;

        $limit = $product->min_stock !== null && $product->min_stock !== '' ? (float)$product->min_stock : 0;

        if ($stock <= $limit) {
            static::createIfMissing(
                'view-product',
                $product->id,
                Lang::get('repair-business.notification.out-of-stock', ['name' => $product->name])
            );
        } else {
            static::clearFor('view-product', $product->id);
        }
    }

    public static function syncInvoiceBalance(Invoice $invoice)
    {
        if ($invoice->active === 'yes' && (float)$invoice->balance > 0) {
            static::createIfMissing(
                'view-invoice',
                $invoice->id,
                Lang::get('repair-business.notification.unpaid-invoice', [
                    'id' => $invoice->id,
                    'amount' => number_format((float)$invoice->balance, 2, '.', ','),
                ])
            );
        } else {
            static::clearFor('view-invoice', $invoice->id);
        }
    }

    public static function newRepair(Repair $repair)
    {
        static::createIfMissing(
            'view-repair',
            $repair->id,
            Lang::get('repair-business.notification.new-repair', ['id' => $repair->id])
        );
    }
}
