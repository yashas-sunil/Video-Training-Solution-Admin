<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
//testing
class Answer extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
