<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verify_Token extends Model
{
    public function token_user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
