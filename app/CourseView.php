<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseView extends Model
{
    protected $table = 'course_views';
    protected $fillable = ['user_id', 'course_id','view_limit'];
}
