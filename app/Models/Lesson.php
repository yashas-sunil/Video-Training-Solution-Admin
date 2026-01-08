<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'chapter_id',
        'lesson_name',
        'lesson_order',
    ];

    protected $dates = ['deleted_at'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function manualContents()
    {
        return $this->hasMany(ChapterManualContent::class);
    }
}