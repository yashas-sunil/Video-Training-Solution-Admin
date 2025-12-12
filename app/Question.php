<?php

namespace App;

use App\Models\User;
use App\Models\Level;
use App\Models\Chapter;
use App\Models\Quiz\UserAnswers;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
     public function userQuestionAnswer()
    {
        return $this->hasMany(UserAnswers::class, 'questions_id', 'id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subjects_id', 'id');
    }
    // public function topic()
    // {
    //     return $this->belongsTo(Topic::class, 'topics_id', 'id');
    // }
    // public function topicQuestion()
    // {
    //     return $this->hasMany(TopicQuestion::class, 'questions_id', 'id');
    // }
    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapters_id', 'id');
    }
    public function subChapter()
    {
        return $this->belongsTo(Subchapter::class, 'subchapter_id', 'id');
     }
    // public function user_chapter()
    // {
    //     return $this->belongsTo(UsersChapter::class, 'chapters_id', 'chapters_id');
    // }
    // public function summary()
    // {
    //     return $this->belongsTo(Summary::class, 'summary_id', 'id');
    // }
    // public function content()
    // {
    //     return $this->belongsTo(Content::class, 'content_id', 'id');
    // }
    public function difficultLevel()
    {
        return $this->belongsTo(DifficultLevel::class, 'difficult_levels_id', 'id');
    }
    // public function concept()
    // {
    //     return $this->hasMany(Concept::class, 'questions_id', 'id');
    // }
    // public function solution()
    // {
    //     return $this->hasOne(Solution::class, 'questions_id', 'id');
    // }

    // public function championship()
    // {
    //     return $this->belongsTo(Championship::class, 'championship_id', 'id');
    // }
    // public function reviewer()
    // {
    //     return $this->belongsTo(Reviewer::class, 'reviewers_id', 'id');
    // }
    // public function author()
    // {
    //     return $this->belongsTo(Author::class, 'authors_id', 'id');
    // }
    // public function language()
    // {
    //     return $this->belongsTo(Language::class, 'languages_id', 'id');
    // }
    // public function importance()
    // {
    //     return $this->belongsTo(Importance::class, 'importance_id', 'id');
    // }
    // public function answerType()
    // {
    //     return $this->belongsTo(AnswerType::class, 'answer_types_id', 'id');
    // }
    public function type()
    {
        return $this->belongsTo(Type::class, 'types_id', 'id');
    }
    // public function paragraphs()
    // {
    //     return $this->belongsTo(Paragraph::class, 'paragraph_id', 'id');
    // }
    // public function questionType()
    // {
    //     return $this->belongsTo(QuestionType::class, 'question_types_id', 'id');
    // }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'questions_id', 'id');
    }
}
