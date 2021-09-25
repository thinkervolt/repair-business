<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
    public function transactions()
    {
        return $this->hasMany('App\InventoryTransaction','product_id','id');
    }

    public function category()
    {
        return $this->hasOne('App\InventoryCategory','id','category_id');
    }
   
    
}
