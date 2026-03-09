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
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
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
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
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
    public function solution()
    {
        return $this->hasOne(Solution::class, 'question_id', 'id');
    }

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
    public function answerType()
    {
        return $this->belongsTo(AnswerType::class, 'answer_types_id', 'id');
    }
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
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
     public function search_max_question_id()
    {
        return $result = Question::max('id');
    }
     public function getQuestionId($qb_id)
    {

        return Question::where('question_banks_id', $qb_id)->select('id')->get()->toArray();
    }

     public function fetchQuestionsByQuestionBankId($questionBank)
    {
        $questionList = Question::with(
            'course',
            'subject',
            'chapter',
            'subChapter',
            'difficultLevel',
            'answerType',
            'createdBy',
            'updatedBy',
            'type',
            'solution'
        )
            ->where('question_banks_id', $questionBank->id)->get();


        // dd($questionBank->id);
        // $questionList = Question::where('question_banks_id', $questionBank->id)->get();
        // dd($questionList);
        // $answer = new Answer();

        if (!empty($questionList)) {
            $question = array();
            $i = 1;
            foreach ($questionList as $results) {

                $temp = array();
                $temp['srno'] = $i;
                $temp['id'] = $results->id;
                $temp['question_number'] = $results->question_number;
                $temp['course_name'] = $results->course->name ?? '';
                // $temp['level_name'] = $results->level->name;
                $temp['subject_name'] = $results->subject->name ?? '';
                $temp['chapter_name'] = $results->chapter->name ?? '';
                $temp['question'] =   !empty($results->question) ? $results->question : '-';
                $temp['summary_name'] =   !empty($results->summary->name) ? $results->summary->name : '-';
                $content_array = [];
                // foreach ($results->topicQuestion as $content_val) {
                //     foreach ($content_val->content as $content_name) {
                //         $content_array[] = $content_name->name;
                //     }
                // }

                $temp['content_name'] = !empty($content_array) ? implode(', ', $content_array) : '-';
                $temp['importance'] = $results->importance;
                $temp['source'] = $results->source;
                $temp['solution_name'] = !empty($results->solution->name) ? $results->solution->name : '-';
                $temp['solution_id'] = !empty($results->solution->id) ? $results->solution->id : 0;
                $temp['solution_image'] = !empty($results->solution->name) ? $results->solution->name : '-';
                $concept_array = [];
                $concept_note_array = [];
                // foreach ($results->concept as $concept_val) {
                //     $concept_array[] = $concept_val->audio->flash_card_name;
                //     $concept_note_array[] = $concept_val->audio->audio_content;
                // }

                $temp['concept_name'] = !empty($concept_array) ? implode(', ', $concept_array) : '-';
                $temp['concept_note'] = !empty($concept_note_array) ? implode(', ', $concept_note_array) : '-';
                $temp['question_type'] =   $results->type->name ?? null;
                // $temp['solution_image'] =   !empty($results->solution->name) ? $results->solution->name : '-';



                $temp['question_banks_id'] = $results->question_banks_id;
                $temp['difficult_level'] = $results->difficultLevel->value;
                // $temp['questions'] = $answer->storagePath($results->question,$questionBank->id);
                // $src = asset('/storage/question/' . $questionBank->id . '/' . $results->id);
                // $temp['question']=   !empty($results->question)?
                // "<a href='".$src."' class='questionimg'
                // data-toggle='tooltip' title='".$results->question."'
                // >". $results->question."</a>":'-';

                // $temp['language'] = $results->language->name;
                $temp['language'] = '-';
                $temp['tags'] = $results->tags;
                // $temp['location'] = $results->location->name;
                $temp['location'] = '-';
                // $temp['reviewer'] = $results->reviewer->name ?? null;
                $temp['author'] = null;

                $topic_array = [];
                // foreach ($results->topicQuestion as $topic_val) {
                //     foreach ($topic_val->topic as $topic_name) {
                //         $topic_array[] = $topic_name->name;
                //     }
                // }
                $temp['topic_name'] = !empty($topic_array) ? implode(', ', $topic_array) : '-';
                $temp['sub_topic_name'] = $results->subChapter->name ?? '-';

                $temp['status'] = $results->status;
                $temp['created_at'] = $results->created_at;
                $temp['created_by'] = $results->createdBy->name;
                $question[] = (object)$temp;
                $i++;
            }

            return $question;
        }
    }
}
