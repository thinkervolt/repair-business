<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    public function customer_data()
    {
        return $this->hasOne('App\Models\Customer','id','customer');
    }

    public function agent_data()
    {
        return $this->hasOne('App\Models\User','id','user');
    }

    public function status_data()
    {
        return $this->hasOne('App\Models\RepairSetting','id','status');
    }

    public function priority_data()
    {
        return $this->hasOne('App\Models\RepairSetting','id','priority');
    }

}
