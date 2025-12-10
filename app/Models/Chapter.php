<?php

namespace App\Models;

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

    public function course() {
        return $this->belongsTo(Course::class);
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
}
