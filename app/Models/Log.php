<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function user_data()
    {
        return $this->hasOne('App\Models\User','id','user');
    }
}
