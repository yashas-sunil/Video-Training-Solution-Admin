<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
        protected $fillable = [
        'batch_name',
        'course_id',
        'start_date',
        'expire_date',
        'scorm_packages_id',
    ];

        public function scorm_packages()
    {
        return $this->belongsTo(ScormPackage::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'batch_student', 'batch_id', 'student_id');
    }

}

