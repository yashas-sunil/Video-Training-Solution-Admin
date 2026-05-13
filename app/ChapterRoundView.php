<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChapterRoundView extends Model
{
    protected $fillable = [
    'user_id',
    'course_id',
    'chapter_id',
    'round_no',
];
}
