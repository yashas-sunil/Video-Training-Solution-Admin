<?php

namespace App;

use App\Models\Level;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;

class Subchapter extends Model
{
     protected $table = 'subchapters';
    const INACTIVE=0;
    const ACTIVE=1;

    protected $fillable = [
        'subject_id',
        'course_id',
        'level_id',
        'chapter_id',
        'title',
        'content',
    ];

    public function subject(){
        return $this->belongsTo(Subject::class,'subjects_id','id');
    }

    // public function topic(){
    //     return $this->hasMany(Topic::class,'subchapter_id');
    // }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
    public function subChapterByName($chapter,$sub_chapter)
    {
        $subChapterValue = SubChapter::whereHas('chapter',function($q) use ($chapter){
                                    $q->where('name', $chapter);
                                    $q->where('status', Chapter::ACTIVE);
                                })
                                ->whereRaw("LOWER(REPLACE(`name`, ' ' ,''))  = ?", [strtolower (str_replace(' ', '', $sub_chapter))])
                                ->where('status', SubChapter::ACTIVE)
                                ->first();
        if(!empty($subChapterValue->id)) 
        {
            return $subChapterValue->id;
        } 
        else 
        {
        	return 0;
        }
    }
}
