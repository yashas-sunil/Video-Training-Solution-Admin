<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    
        protected $table = 'quiz_results';
  protected $fillable = [
        'user_id',
        'quiz_name',
        'chapter_name',
        'total_questions',
        'correct_answers',
        'score_percent',
        'question_ids',
        'wrong_answers',
        'attempt_number'
    ];
    protected $casts = [
    'question_ids' => 'array', // ✅ Auto-convert to array when accessing
];
}
