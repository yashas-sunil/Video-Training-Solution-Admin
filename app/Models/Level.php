<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    const ACTIVE=1;
    const INACTIVE=0;
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $appends = [
        'has_package'
    ];

    public function course() {
        return $this->belongsTo(Course::class);
    }

    public function subjects() {
        return $this->hasMany(Subject::class);
    }

    public function scopeOfCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeSearch($query, $searchText)
    {
        return $query->where('name', 'LIKE', '%'.$searchText.'%');
    }
    
    public function getHasPackageAttribute()
    {
        $package_withLevel = Package::where('level_id',$this->id)->get();
            if(count($package_withLevel)){
                return true;
            }
            else{
                
                return false;
            }
    }
        public function levelByName($course,$value){
        $level=Level::whereHas('course',function($q) use ($course){
            $q->where('name', $course);
            $q->where('status', Course::ACTIVE);

        })->where('name',$value)->where('status',Level::ACTIVE)->first();
        if(!empty($level->id)){
            return $level->id;
        }else{
             return 0;
        }

    }
}
