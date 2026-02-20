<?php

namespace App\Models;
use App\Models\Course;
use App\Models\Lesson;
use App\Question;
use App\ScormPackage as AppScormPackage;
use App\Summary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
      const INACTIVE=0;
    const ACTIVE=1;
    protected $appends = [
        'has_package'
    ];

    protected $fillable = [
        'course_id',
        'name',
        'folder_name',
        'launch_file',
    ];
    public function course() {
        return $this->belongsTo(Course::class);
    }

    public function courses()
{
    return $this->belongsTo(AppScormPackage::class, 'id');
}
    public function level() {
        return $this->belongsTo(Level::class);
    }

    
    /******Added BY TE *******/

    public function package_type(){
        return $this->belongsTo(PackageType::class);
    }

    /********TE Ends*******/

    public function subject() {
        return $this->belongsTo(Subject::class);
    }
    public function module() {
        return $this->hasMany(Module::class);
    }

    public function scopeOfCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeOfLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    public function scopeOfSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeSearch($query, $searchText)
    {
        return $query->where('name', 'LIKE', '%'.$searchText.'%');
    }

    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function manualContents()
    {
        return $this->hasMany(ChapterManualContent::class)
        ->orderBy('id', 'asc');
    }
    public function getHasPackageAttribute()
    {
        $package_withChapter = Package::where('chapter_id',$this->id)->get();
            if(count($package_withChapter)){
                return true;
            }
            else{
                
                return false;
            }
    }
      public static function findMaxChapterQuestionId($chapterId)
    {
        return Question::where('chapter_id', $chapterId)
            ->whereNull('deleted_at')
            ->max('question_number') ?? 0;
    }
      public function chapterByName($subject,$chapter) 
    {
        $chapterValue = Chapter::whereHas('subject',function($q) use ($subject){
                            $q->where('name', $subject);
                            $q->where('status', Subject::ACTIVE);
                        })
                        ->whereRaw("LOWER(REPLACE(`name`, ' ' ,''))  = ?", [strtolower (str_replace(' ', '', $chapter))])
                        ->where('status', Chapter::ACTIVE)
                        ->first();

        if(!empty($chapterValue->id)) 
        {
            return $chapterValue->id;
        } 
        else 
        {
        	return 0;
        }
    }
      public function summaryByChapterId($chapter_id) 
    {
        $summary_id = Summary::where('chapters_id', $chapter_id)
                              ->where('status', Summary::ACTIVE)
                              ->value('id');

        if(isset($summary_id))
        {
            return $summary_id;
        }
        else
        {
            return 0;
        }
    }
    public function lessons()
{
    return $this->hasMany(Lesson::class, 'chapter_id');
}
}
