<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    protected $guarded = ['id'];
    const ACTIVE = 1;
    const INACTIVE = 0;
    
    public function course(){
        return $this->belongsTo(Course::class,'course_id','id');
    }
    // public function theme(){
    //     return $this->belongsTo(SubjectTheme::class,'theme_id','id');
    // }
    public function level(){
        return $this->belongsTo(Level::class,'level_id','id');
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    // public function attemptSubject(){
    //     return $this->belongsTo(AttemptSubject::class,'id','subject_id');
    // }
    public function subjectByName($name)
    {

        $subject = Subject::where('name', $name)->where('status', Subject::ACTIVE)->first();

        if (!empty($subject->id)) {

            return $subject->id;
        } else {
            return 0;
        }
    }
    public function subjectByCourse($id)
    {

        $subject = Subject::where('course_id', $id)->where('status', Subject::ACTIVE)->get();
        $finalArray = array();
        if (count($subject) > 0) {

            foreach ($subject as $val) {
                $array['id'] = $val->id;
                $array['name'] = $val->name;
                $array['free_trial'] = $val->free_trial;
                $array['is_paid'] = $val->is_paid;
                $finalArray[] = $array;
            }
        }
        return $finalArray;
    }
    // public function userSubjectByCourse($level_id,$id,$users_id)
    // {

    //     $subject = Subject::where('course_id', $id)->where('level_id', $level_id)->where('status', Subject::ACTIVE)->get();
    //     $finalArray = array();
    //     if (count($subject) > 0) {

    //         foreach ($subject as $val) {
    //             $theme = SubjectTheme::where('id', $val->theme_id)->first();
    //             $grade = SubjectAvgGrade::where('user_id', $users_id)->where('subject_id', $val->id)->value('avg_grade') ?? Grade::orderBy('id','desc')->value('grade');
    //             $array['id'] = $val->id;
    //             $array['name'] = $val->name;
    //             $array['icon_name'] = $val->icon_name;
    //             $array['free_trial'] = $val->free_trial;
    //             $array['is_paid'] = $val->is_paid;
    //             $array['grade'] = $grade;
    //             $array['grade_color'] =  Grade::getGradeColor($grade);
    //             $chapters = Chapter::where('subjects_id', $val->id)->where('status', Chapter::ACTIVE)->get();
    //             $totalChapters = $chapters->count();
    //             $chapterIds = $chapters->pluck('id');
    //             $topics = Topic::whereHas('content', function ($query) {
    //                 $query->where('status', 1);
    //             })->whereIn('chapters_id', $chapterIds)->where('status', Topic::ACTIVE)->get();
    //             $totalTopics = $topics->count();
    //             $topicIds = $topics->pluck('id');
    //             $toatlUserSubjectChapters= UsersChapter::whereHas('chapter',function($q)use($val){
    //                 $q->where('subjects_id',$val->id);
    //             })->where('users_id',$users_id)->where('status', UsersChapter::COMPLETED)->count();
    //             $toatlUserSubjecttopics= UsersTopic::whereIn('topics_id', $topicIds)->where('users_id',$users_id)->where('status', UsersTopic::COMPLETED)->count();
    //             $array['totalChapters'] = $totalChapters;
    //             $array['toatlUserSubjectChapters'] = $toatlUserSubjectChapters;
    //             $array['user_subject_chapters_percentage'] =$totalTopics?(float)(($toatlUserSubjecttopics/$totalTopics)*100):0;
    //             if($theme != null){
    //                 $array['theme']['theme_id'] = $theme->id;
    //                 $array['theme']['primary_color'] = $theme->primary_color;
    //                 $array['theme']['doodles_color'] = $theme->doodles_color;
    //                 $array['theme']['back_color'] = $theme->back_color;
    //                 $array['theme']['font_color'] = $theme->font_color;
    //                 $array['theme']['created_at'] = $theme->created_at;
    //                 $array['theme']['updated_at'] = $theme->updated_at;
    //             }
    //             $finalArray[] = $array;
    //         }
    //     }
    //     return $finalArray;
    // }

    public function subjectByCourseName($course,$level,$subject){
        $subject = Subject::whereHas('course',function($q) use ($course){
            $q->where('name', $course);
            $q->where('status', Course::ACTIVE);

        })->whereHas('level',function($q) use ($level){
            $q->where('name', $level);
            $q->where('status', Level::ACTIVE);

        })->where('name', $subject)->where('status', Subject::ACTIVE)->first();
        if (!empty($subject->id)) {
            return $subject->id;
        } else {
            return 0;
        }

    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'subjects_id');
    }

    // public function quizzes()
    // {
    //     return $this->hasMany(QuizHistory::class,'subject_id','id');
    // }

    public function getSubjectProgress($subjectProgressArray){
        $avgPercentage=0;
        if(!empty($subjectProgressArray)){
            $i=0;
            $user_subject_chapters_percentage=0;
            foreach($subjectProgressArray as $val){
                $i++;
               $user_subject_chapters_percentage+=$val['user_subject_chapters_percentage'];
            }
            $avgPercentage=$user_subject_chapters_percentage/$i;

        }
        return $avgPercentage;

    }
     public function courseByName($value)
    {
        $course = Course::where('name',$value)
                        ->where('status',Course::ACTIVE)
                        ->first();
                        
        if(!empty($course->id))
        {
            return $course->id;
        }
        else
        {
             return 0;
        }
    }
}
