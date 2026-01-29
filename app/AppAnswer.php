<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppAnswer extends Model
{
     protected $guarded=['id'];
    public function search_max_question_answer_id() {
        return $result = Answer::max('id');
    }
    public function answer_type(){
        return $this->belongsTo(AnswerType::class,'answer_types_id','id');
    }
    public function bulkAnswersUpload($insert_data) {

        return $result = Answer::insert($insert_data);
    }
    public function deleteQuestionIdAnswers($questions) {
        $count= count($questions);
        $count_array=array();
        foreach($questions as $id)
        {  
            $results=Answer::where('questions_id',$id)->delete();
            $count_array[]=$results;
        }
        if($count==count($count_array))
        {
            return 1;
        }
        
    }
    public function storagePath($editor, $question_id) {

        // $questionbank_path = new QuestionBank();
        // $storage = $question_id;
        preg_match_all('@src="([^"]+)"@', $editor, $result);
        $file_names = array_pop($result);

  

        $insert_data = array();
        $insert_data['editor'] = $editor;


        $temp=array();
      
        foreach ($file_names as $file_key => $file) {
            
            
            
            $newimagepath = Storage::disk('public')->url($file);
            $oldimagepath = $file;
       
          if(!in_array($oldimagepath, $temp)){
            $insert_data['editor'] = str_replace($oldimagepath, $newimagepath, $insert_data['editor']);
           
          }
          $temp[]=$file;
          
        }  
       

        return $insert_data['editor'];
    }
    public static function storagePathQuestionAnswer($editor) 
    {
        preg_match_all('@src="([^"]+)"@', $editor, $result);
        $file_names = array_pop($result);
        $newimagepath=array();
        foreach ($file_names as $file_key => $file) {
           
            $newimagepath[] = Storage::disk('public')->url($file);
        }  
        return $newimagepath;
    }
    public static function storagePathAnswer($editor) {

        preg_match_all('@src="([^"]+)"@', $editor, $result);
        $file_names = array_pop($result);
        $newimagepath=null;
        foreach ($file_names as $file_key => $file) {
           
            $newimagepath[] = Storage::disk('public')->url($file);
        }  
        return $newimagepath;
    }
    public function fetchAnswersByQuestionId($id) {
        // $results = Answer::where('questions_id', $id)->get()->toArray();
        $results = Question::with('answers')->where('id', $id)->first();

        $answers = array();
      
        foreach ($results->answers as $val) {
            $temp = array();
            $temp['correctanswers'] = $val->correctans;
            $temp['answer'] = $this->storagepath($val->answer, $id);
            $temp['answer_id'] = $val->id;
            $temp['question'] = $results->question;
            $answers[] = $temp;
        }

        return $answers;
    }
}
