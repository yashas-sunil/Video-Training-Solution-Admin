<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopicQuestion extends Model
{
    
 public function bulkTopicQuestionUpload($insert_topic_question)
    {
        return $result = TopicQuestion::insert($insert_topic_question);
    }
}
