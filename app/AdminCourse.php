<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdminCourse extends Model
{
    
     protected $fillable = ['title', 'description', 'thumbnail', 'created_by', 'updated_by','training_link', 'access_password'];

        public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')
            ->withPivot('enrolled_at', 'expire_date')
            ->withTimestamps();
    }
}
