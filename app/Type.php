<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
     const ACTIVE=1;
    const INACTIVE=0;

    public function typeByName($name) {  
    $type = Type::where('name',$name)->where('status', Type::ACTIVE)->first();
    
        if(!empty($type->id)) {
            
            return $type->id;
        } else {
            return 0;
        }
    }
}
