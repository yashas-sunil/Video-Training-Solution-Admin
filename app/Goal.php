<?php

namespace App;

use App\Models\User;
use App\Models\Course;
use App\DifficultLevel;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
protected $connection = 'second_db';
     public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
  public function level()
    {
        return $this->belongsTo(DifficultLevel::class, 'level_id');
    }
}
