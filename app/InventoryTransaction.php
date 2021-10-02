<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    public function product()
    {
        return $this->hasOne('App\InventoryProduct','id','product_id');
    }
}
