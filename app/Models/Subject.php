<?php

namespace App\Models;

use App\ScormPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    // protected $connection = 'second_db';

    // use SoftDeletes;

    protected $guarded = ['id'];
    const ACTIVE = 1;
    const INACTIVE = 0;
    protected $appends = [
        'has_package'
    ];

    /**
     * Scope a query to only include all subjects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllSubjects($query)
    {
        return $query;
    }

    public function level() {
        return $this->belongsTo(Level::class);
    }

    public function course() {
        return $this->belongsTo(ScormPackage::class);
    }

    /******Added BY TE  *****/

    public function package_type(){
        return $this->belongsTo(PackageType::class);
    }   

    /**************TE Ends**************** */

    public function language() {
        return $this->belongsTo(Language::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class);
    }
    public function subjects() {
        return $this->hasMany(Subject::class);
    }

    public function scopeOfCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeOfLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    public function scopeSearch($query, $searchText)
    {
        return $query->where('name', 'LIKE', '%'.$searchText.'%');
    }
    public function getHasPackageAttribute()
    {
        $package_withSubject = Package::where('subject_id',$this->id)->get();
            if(count($package_withSubject)){
                return true;
            }
            else{
                
                return false;
            }
    }
  public function subjectByCourseName($course,$subject){

    $course = trim($course);
    $subject = trim($subject);

    $courseData = ScormPackage::where('title',$course)->first();

    if(!$courseData){
        return 0;
    }

    $subjectData = Subject::where('name',$subject)
            ->where('course_id',$courseData->id)
            ->where('status',1)
            ->first();
    //dd($subjectData);
    if($subjectData){
        return $subjectData->id;
    }

    return 0;
}

public function scormPackage()
{
    return $this->belongsTo(ScormPackage::class,'scorm_package_id');
}
}
