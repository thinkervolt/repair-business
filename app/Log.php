<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function user_data()
    {
        return $this->hasOne('App\User','id','user');
    }
}
