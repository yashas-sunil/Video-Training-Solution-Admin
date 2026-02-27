<?php

namespace App;

use App\Content;
use App\UsersTopic;
use App\Models\User;
use App\Models\Level;
use App\TopicQuestion;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
      protected $guarded=['id'];
    const ACTIVE=1;
    const INACTIVE=0;
    public function chapter()
    {
        return $this->belongsTo(Chapter::class,'chapters_id','id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class,'subject_id','id');
    }
    public function audios()
    {
        return $this->hasOne(Audio::class, 'topic_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class,'course_id','id');
    }
    public function level()
    {
        return $this->belongsTo(Level::class,'level_id','id');
    }
    public function content()
    {
        return $this->hasOne(Content::class,'topics_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function userTopic()
    {
        return $this->hasOne(UsersTopic::class,'topics_id','id');
    }
    public function subChapter()
    {
        return $this->belongsTo(Subchapter::class,'subchapter_id','id');
    }
    public function topicByName($name,$subject) 
    {
        $topic = Topic::where('name',$name)->where('subjects_id',$subject)->where('status', Topic::ACTIVE)->first();
        if(!empty($topic->id)) 
        {
        	return $topic->id;
        } 
        else 
        {
        	return 0;
        }
    }
    public function topicByChapterNameCopy($chapter,$topic)
    {
        $topicValue = Topic::whereHas('chapter',function($q) use ($chapter){
                                $q->where('name', $chapter);
                                $q->where('status', Chapter::ACTIVE);

                            })->where('name', $topic)
                            ->where('status', Topic::ACTIVE)
                            ->first();

        if (!empty($topicValue->id)) 
        {
            return $topicValue->id;
        } 
        else 
        {
            return 0;
        }

    }
    public function topicByChapterName($chapter,$topic)
    {
        $topicValue = Topic::whereHas('chapter',function($q) use ($chapter){
                            $q->whereRaw("LOWER(REPLACE(`name`, ' ' ,''))  = ?", [strtolower(str_replace(' ', '', $chapter))]);
                            $q->where('status', Chapter::ACTIVE);

                        })->whereRaw("LOWER(REPLACE(`name`, ' ' ,''))  = ?", [strtolower(str_replace(' ', '', $topic))])
                        ->where('status', Topic::ACTIVE)
                        ->first();

        if (!empty($topicValue->id)) 
        {
            return $topicValue->id;
        } 
        else 
        {
            return 0;
        }
    }
    public static function noOfQuestions($topics_id)
    {
        return TopicQuestion::where('topics_id', $topics_id)->count();
    }
    public function contentByTopicId($topic_id)
    {
        $content_id = Content::whereIn('topics_id', $topic_id)
                            ->where('status', Content::ACTIVE)
                            ->pluck('id')
                            ->toArray();

        if (!empty($content_id)) 
        {
            return $content_id;
        } 
        else 
        {
            return 0;
        }
    }
}
