<?php

namespace App;

use App\Question;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;

class ChaptersQuestion extends Model
{
    const INACTIVE=0;
    const ACTIVE=1;
    public function chapter(){
        return $this->belongsTo(Chapter::class,'chapters_id','id');
    }
    public function question(){
        return $this->belongsTo(Question::class,'questions_id','id');
    }

    public function findMaxChapterQuestionId(){
        return ChaptersQuestion::max('id');
    }

    public function bulkChapterQuestionUpload($insert_chapter_question)
    {
        return $result = ChaptersQuestion::insert($insert_chapter_question);
    }
    public static function getlimitByLength($length)
    {
        if ($length == 1) {
            return [5];
        } else if ($length == 2) {
            return [2, 3];
        } else if ($length == 3) {
            return [2, 2, 1];
        } else if ($length == 4) {
            return [2, 1, 1];
        }else {
            return array_fill(0, $length, 1);
        }
    }
}
