<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AnswerType extends Model
{
     protected $guarded=['id'];
    const ACTIVE=1;
    const INACTIVE=0;
    public function answerTypeByName($name) {
         
        $answertype = AnswerType::where('name',$name)->where('status', AnswerType::ACTIVE)->first();
        
         if(!empty($answertype->id)) {
             
             return $answertype->id;
         } else {
             return 0;
         }
        
       
         
     }
     public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function questions()
    {
        return $this->hasMany(Question::class, 'answer_types_id', 'id');
    }
}
