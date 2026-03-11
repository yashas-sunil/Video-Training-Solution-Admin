<?php

namespace App\Models;

use App\ScormPackage;
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
        return $this->belongsTo(ScormPackage::class);
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
      public function levelByName($course,$value)
{
    $package = ScormPackage::where('title', $course)
                    ->where('status', 1)
                    ->first();

    if(!$package){
        return 0;
    }

    $level = Level::where('course_id', $package->id)
                ->where('name', $value)
                ->where('status', 1)
                ->first();

    if(!empty($level->id)){
        return $level->id;
    }else{
        return 0;
    }


    }
}
