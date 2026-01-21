<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentToken extends Model
{
    protected $fillable = [
        'student_id',
        'email',
        'phone',
        'token'
    ];
}
