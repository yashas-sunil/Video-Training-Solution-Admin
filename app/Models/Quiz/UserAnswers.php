<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;

class UserAnswers extends Model
{

    protected $table = 'users_answers';

    protected $primaryKey = 'id';

    protected static $logAttributes = ['*'];

    protected $fillable = [

        'answers_id',
        'correct_answers_id',
        'question_id',
        'option_id',
        'user_id',

        'marks',
        'negative_marks',
        'is_correct',
        'message',

        'time_taken',
        'user_question_status',
        'is_cumulative_question',
        'status',

        'chapters_questions_id',
        'submitted_quiz_id',
        'submitted_objective_id',
        'submitted_block_id',
        'submitted_championship_id',
        'submitted_tournament_id',
        'user_test_id',
        'user_question_id',

        'esec',
        'rsec',
        'mil',

        'created_by',
        'updated_by',
    ];
    public function getQuestion(){
        return $this->belongsTo('App\Models\Quiz\Question','question_id','id');
    }


    public function getUser(){
        return $this->belongsTo('App\User','user_id','id');
    }

    public function getUserTest(){
        return $this->belongsTo('App\Models\Quiz\UserTests','user_test_id','id');
    }

    public function getAnswer(){
        return $this->belongsTo('App\Models\Quiz\Answer','option_id','id');
    }


}
