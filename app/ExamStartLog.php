<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamStartLog extends Model
{
    protected $fillable = [
    'user_id',
    'course_id',
    'subject_id',
    'chapter_id',
    'test_id',
    'open_count'
];
}
