<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //Fixed and works
    public function items(){
       return $this->belongsToMany('App\Item','items_tags','tag_id','item_id');

    }

    //
}
