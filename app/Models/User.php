<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    const ROLE_ADMIN = 1;
    const ROLE_COURSE_ADMIN = 2;
    const ROLE_BUSINESS_ADMIN = 3;
    const ROLE_PLATFORM_ADMIN = 4;
    const ROLE_STUDENT = 5;
    const ROLE_PROFESSOR = 6;
    const ROLE_AGENT = 7;
    const ROLE_REPORT_ADMIN = 8;
    const ROLE_CONTENT_MANAGER = 9;
    const ROLE_FINANCE_MANAGER = 10;
    const ROLE_BRANCH_MANAGER = 11;
    const ROLE_THIRD_PARTY_AGENT = 12;
    const ROLE_ASSISTANT = 13;
    const ROLE_REPORTING=14;
    const ROLE_BACKOFFICE_MANAGER=15;
    const ROLE_JUNIOR_ADMIN=16;

    const ROLE_ADMIN_TEXT = 'Super Admin';
    const ROLE_COURSE_ADMIN_TEXT = 'Course Admin';
    const ROLE_BUSINESS_ADMIN_TEXT = 'Business Admin';
    const ROLE_PLATFORM_ADMIN_TEXT = 'Platform Admin';
    const ROLE_STUDENT_TEXT = 'Student';
    const ROLE_PROFESSOR_TEXT = 'Professor';
    const ROLE_AGENT_TEXT = 'Agent';
    const ROLE_REPORT_ADMIN_TEXT = 'Report Admin';
    const ROLE_CONTENT_MANAGER_TEXT = 'Content Manager';
    const ROLE_FINANCE_MANAGER_TEXT = 'Finance Manager';
    const ROLE_BRANCH_MANAGER_TEXT = 'Branch Manager';
    const ROLE_THIRD_PARTY_AGENT_TEXT = 'Third Party Agent';
    const ROLE_ASSISTANT_TEXT = 'Assistant Manager';
    const ROLE_REPORTING_TEXT ='Reporting';
    const ROLE_BACKOFFICE_MANAGER_TEXT='Back Office Manager';
    const ROLE_JUNIOR_ADMIN_TEXT='Junior Admin';


    public function student() {
        return $this->hasOne(Student::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function private_coupon()
    {
        return $this->hasOne(PrivateCoupon::class);
    }

    public function video()
    {
        return $this->hasOne(Video::class, 'published_user_id');
    }

    public function studyMaterialOrderLog() {
        return $this->hasOne(StudyMaterialOrderLog::class);
    }


public function courses()
{
    return $this->belongsToMany(Course::class, 'user_courses')
        ->withPivot('enrolled_at', 'expire_date')
        ->withTimestamps();
}


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role','status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
