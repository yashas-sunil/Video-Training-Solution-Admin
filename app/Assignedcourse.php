<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Assignedcourse extends Model
{
    protected $table = 'assigned_courses';

    protected $fillable = ['user_id', 'course_id', 'expire_date', 'enrolled_at'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(ScormPackage::class, 'course_id');
    }

    public function progress()
    {
        return $this->hasMany(CourseProgress::class, 'course_id', 'course_id')
            ->where('user_id', auth()->id());
    }
    public function courseView()
{
    return $this->hasMany(CourseView::class, 'course_id', 'course_id');
}

}
