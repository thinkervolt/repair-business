<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairItem extends Model
{
    public function agent_data()
    {
        return $this->hasOne('App\Models\User','id','user');
    }
}
