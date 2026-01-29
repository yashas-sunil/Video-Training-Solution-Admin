<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DifficultLevel extends Model
{
protected $guarded=['id'];
    const ACTIVE=1;
    const INACTIVE=0;
    const EASY=1;
    const MEDIUM=2;
    const HARD=3;

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function difficultByName($name){
        $difficultLevel=DifficultLevel::where('name',$name)->where('status',DifficultLevel::ACTIVE)->first();
        if(!empty($difficultLevel->id)){
            return $difficultLevel->id;
        }else{
            return 0;
        }
    }
    public function question(){
        return $this->hasOne(Question::class);
    }
    
}
