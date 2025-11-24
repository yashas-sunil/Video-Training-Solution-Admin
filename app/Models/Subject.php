<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    protected $connection = 'second_db';

    // use SoftDeletes;

    protected $guarded = ['id'];
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
        return $this->belongsTo(Course::class);
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
}
