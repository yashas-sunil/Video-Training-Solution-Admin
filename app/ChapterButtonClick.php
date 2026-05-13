<?php

namespace App;

use App\Models\Chapter;
use App\Models\User;
use App\ScormPackage;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterButtonClick extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'course_id',
        'button_type',
        'click_count',
        'last_clicked_at'
    ];

    protected $casts = [
        'last_clicked_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function course()
    {
        return $this->belongsTo(ScormPackage::class);
    }

    // Helper method to record click
    public static function recordClick($userId, $chapterId, $courseId, $buttonType)
    {
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'chapter_id' => $chapterId,
                'button_type' => $buttonType
            ],
            [
                'course_id' => $courseId,
                'click_count' => DB::raw('click_count + 1'),
                'last_clicked_at' => now()
            ]
        );
    }
}
