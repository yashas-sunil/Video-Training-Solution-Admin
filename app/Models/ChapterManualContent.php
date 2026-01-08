<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterManualContent extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'chapter_id',
        'lesson_id',
        'content_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function isLessonContent()
    {
        return !is_null($this->lesson_id);
    }

    public function isChapterContent()
    {
        return is_null($this->lesson_id);
    }
}

