<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminTestResult extends Model
{
    protected $table = 'admin_test_results';

    protected $fillable = [
        'test_id',
        'user_id',
        'total_no_ques',
        'answer_id',
        'answer_selected'
    ];


}
