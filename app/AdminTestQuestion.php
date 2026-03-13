<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminTestQuestion extends Model
{
    protected $table = 'admin_test_questions';
    
    protected $fillable = [
        'admin_test_id',
        'question_id'
    ];

    public function adminTest()
    {
        return $this->belongsTo(\App\admin_test::class, 'admin_test_id');
    }

    public function question()
    {
        return $this->belongsTo(\App\Question::class, 'question_id');
    }
}
