<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizAttemptAnswer extends Model
{
     protected $fillable = [
        'user_id',
        'quiz_name',
        'chapter_name',
        'question_id',
        'user_answer',
        'attempt_number',
        'correct_answer',
        'is_correct'
    ];
}
