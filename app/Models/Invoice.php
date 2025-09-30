<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public function status_data()
    {
        return $this->hasOne('App\Models\InvoiceSetting','id','status');
    }

    public function items()
    {
        return $this->hasMany('App\Models\InvoiceItem','invoice','id');
    }
}
