<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    public function customer_data()
    {
        return $this->hasOne('App\Customer','id','customer');
    }

    public function agent_data()
    {
        return $this->hasOne('App\User','id','user');
    }

    public function status_data()
    {
        return $this->hasOne('App\RepairSetting','id','status');
    }

    public function priority_data()
    {
        return $this->hasOne('App\RepairSetting','id','priority');
    }

}
