<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class admin_test extends Model
{
    protected $table = 'admin_test';
    
    protected $fillable = [
        'test_name',
        'course_id',
        'subject_id',
        'total_ques_count',
        'easy_count',
        'medium_count',
        'hard_count',
        'status'
    ];

    public function course()
    {
        return $this->belongsTo(\App\Course::class, 'course_id');
    }

    /**
     * Get subject IDs as array
     */
    public function getSubjectIdsArray()
    {
        return $this->subject_id ? explode(',', $this->subject_id) : [];
    }
}
