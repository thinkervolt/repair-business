<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
    public function transactions()
    {
        return $this->hasMany('App\Models\InventoryTransaction','product_id','id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\InventoryCategory','id','category_id');
    }
   
    
}
