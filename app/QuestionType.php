<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
     const ACTIVE=1;
    const INACTIVE=0;
    //Question Type
    const Practical=1;
    const Theory=2;

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function questionTypeByName($name)
    {
        $questiontype = QuestionType::where('name', $name)->where('status', QuestionType::ACTIVE)->first();

        if (!empty($questiontype->id)) {

            return $questiontype->id;
        } else {
            return 0;
        }
    }
}
