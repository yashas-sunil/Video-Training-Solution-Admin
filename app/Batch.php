<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\ScormPackage;

class Batch extends Model
{
    protected $table = 'batches';

    protected $fillable = [
        'batch_name',
        'start_date',
        'expire_date',
    ];

    protected $dates = [
        'start_date',
        'expire_date',
        'created_at',
        'updated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // 🔹 Multiple Courses per Batch
    public function courses()
    {
        return $this->belongsToMany(
            ScormPackage::class,
            'batch_course',       // pivot table
            'batch_id',
            'scorm_package_id'
        );
    }

    // 🔹 Students assigned to Batch
    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'batch_student',
            'batch_id',
            'student_id'
        );
    }
}
