<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public function status_data()
    {
        return $this->hasOne('App\InvoiceSetting','id','status');
    }

    public function items()
    {
        return $this->hasMany('App\InvoiceItem','invoice','id');
    }
}
