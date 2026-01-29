<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class QuestionBankHistory extends Model
{
     protected $guarded=['id'];
    public function addQuestionBankHistory($input,$insert_qb,$status) { 
         
        $data = new QuestionBankHistory();
        $data->name   = $input['name'];
        $data->question_banks_id = $insert_qb;
        // $data->qbtype = $input['question_type'];
        // $data->languages_id = $input['languages_id'];
        $data->qbstatus = $input['category'];
        $data->status = $status;
        $data->created_by = Auth::id();
        $data->updated_by =Auth::id();
        
        $data->save();
        if ($data->id) {
            return $data->id;
        } else {
            return 0;
        }
     }

     public function updateQuestionBankHistory($input,$qbid,$status,$history_id,$filepath='',$message) { 
          
        $data = QuestionBankHistory::where('id', $history_id)
                ->update(['question_banks_id' => $qbid,
                          'file' => $filepath,
                          'message'=>$message,
                          'status' =>$status
                        ]);
        if ($data == 1) {
            return 1;
        } else {
            return 0;
        }
     }
}
