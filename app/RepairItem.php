<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepairItem extends Model
{
    public function agent_data()
    {
        return $this->hasOne('App\User','id','user');
    }
}
