<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //works
    public function item(){
        return $this->belongsTo('App\Item','item_id','id');
    }
}
