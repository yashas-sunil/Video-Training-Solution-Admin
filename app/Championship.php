<?php

namespace App;

use Exception;
use Carbon\Carbon;
use App\Models\Level;
use App\Models\Quiz\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
class Championship extends Model
{
     const Active   = 1;
    const InActive = 0;

    const Free     = 1;
    const Standard = 2;
    const Premium  = 3;

    const Subjective           = 1;
    const Objective            = 2;
    const Subjective_Objective = 3;

    const SCHEDULE  = 0;
    const LIVE      = 1;
    const COMPLETED = 2;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function chapters()
    {
        return $this->belongsToMany(Chapter::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function attempt()
    {
        return $this->belongsTo(Attempt::class);
    }

    public function championship_histories()
    {
        return $this->hasMany(ChampionshipHistory::class, 'championship_id', 'id');
    }
    public function championship_history()
    {
        return $this->hasOne(ChampionshipHistory::class, 'championship_id', 'id');
    }

    public function championship_timeline(){
        return $this->hasOne(UserChampionshipTimeline::class,'championship_id','id');
    }
    public function championshipTimelineForUser($userId)
    {
        return $this->hasOne(UserChampionshipTimeline::class, 'championship_id', 'id')
                    ->where('user_id', $userId);
    }

    public static function uploadQuestionPDF($file)
    {
        try {
            $originalName        = $file->getClientOriginalName();
            $originalName        = dechex(time()) . '_' . str_replace(' ', '_', $originalName);
            $path                = Storage::putFileAs('public/championship/subjective_pdf', $file, $originalName); //store subjective question pdf
            $return['file_name'] = $originalName;
            $return['status']    = 200;
            return $return; //return success along with file name
        } catch (Exception $e) {
            $return['error']  = $e->getMessage();
            $return['status'] = 422;
            return $return;
        }
    }

    public static function createChampionship($input, $excel_file_name, $pdf_file_name = null)
    {
        try {

            $championship                           = new Championship();
            $championship->name                     = $input['name'];
            $championship->start_date               = $input['start_date'];
            $championship->end_date                 = $input['end_date'];
            $championship->user_test_type           = $input['is_paid'];
            $championship->status                   = $input['status'];
            $championship->course_id                = $input['course'];
            $championship->total_marks              = $input['subjective_marks'] ?? 0;
            $championship->subjective_marks         = $input['subjective_marks'] ?? 0;
            $championship->objective_marks          = 0;
            $championship->level_id                 = $input['level'];
            $championship->attempt_id               = $input['attempt'];
            $championship->total_question           = $input['total_questions'] ?? 0;
            $championship->overall_time             = ($input['duration'] * 60);
            $championship->test_type                = $input['test_type'];
            $championship->subjective_question_pdf  = $pdf_file_name ?? null;
            $championship->objective_question_excel = $excel_file_name ?? null;
            $championship->total_subjective_questions = $input['no_of_questions'] ?? 0;
            $championship->excel_question_select    = $input['excel_question_select'] ?? 0;
            $championship->qb_question_select       = $input['qb_question_select'] ?? 0;
            $championship->save();

            $championship = Championship::find($championship->id);
            $championship->subjects()->attach($input['subject']);
            $championship->chapters()->attach($input['chapter']);
            return response()->json([
                'message'      => 'Championship successfully created',
                'name'         => $input['name'],
                'id'           => $championship->id,
                'championship' => $championship,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public static function createChampionshipQB($championship)
    {
        try {
            $rule_structure = app(TournamentApiController::class)->storeRule(new Request([
                'rule_id' => QuizRule::Championship,
                'rule'    => null,
            ]), 'championship', $championship['qb_question_select']);

            if ($rule_structure == 0) {
                return response()->json(['message' => 'Rule not found'], 422);
            }
            $chapterIds = DB::table('championship_chapter')
                ->where('championship_id', $championship['id'])
                ->pluck('chapter_id')
                ->toArray();

            $response = app('App\Http\Controllers\API\ChapterQuestionAnswerApiController')
                ->index(new Request([
                    'flag'           => 'get_question_set',
                    'total_question' => $championship['qb_question_select'],
                    'quiz_type_id'   => QuizRule::Championship,
                    'rule'           => $rule_structure,
                    'id'             => $chapterIds,
                    'championship'   => $championship,
                ]));

            if ($response->status() !== 200) {
                return response()->json(['message' => 'Insufficient questions found in championship'], 422);
            }

            $questions = $response->getData()->data;
            if (count($questions) !== $championship['total_question']) {
                return response()->json(['message' => 'Insufficient questions found'], 422);
            }

            $currentTimestamp = now();
            $mergedQuestions  = [];

            $questionChapterPairs = collect($questions)->map(function ($q) {
                return [
                    'questions_id' => $q->id,
                    'chapters_id'  => $q->chapters_id,
                ];
            });
            $chaptersQuestions = Chapter::whereIn('questions_id', $questionChapterPairs->pluck('questions_id'))
                ->whereIn('chapters_id', $questionChapterPairs->pluck('chapters_id'))
                ->orderByDesc('created_at')
                ->get()
                ->groupBy(fn($item) => $item->chapters_id . '_' . $item->questions_id);
            
            $totalMarks=0;
            foreach ($questions as $item) {
                $key = $item->chapters_id . '_' . $item->id;

                $chaptersQuestionId = optional($chaptersQuestions->get($key)[0] ?? null)->id;

                $mergedQuestions[] = [
                    'created_at'            => $currentTimestamp,
                    'updated_at'            => $currentTimestamp,
                    'chapters_id'           => $item->chapters_id,
                    'questions_id'          => $item->id,
                    'championship_id'       => $championship['id'],
                    'chapters_questions_id' => $chaptersQuestionId,
                ];

                $totalMarks += (float) $item->question->difficult_level->correct_marks;
            }
            ChampionshipQuestion::insert($mergedQuestions);
            DB::table('championships')
            ->where('id', $championship['id'])
            ->update([
                'total_marks' => DB::raw("IFNULL(total_marks, 0) + {$totalMarks}"),
                'objective_marks' => DB::raw("IFNULL(objective_marks, 0) + {$totalMarks}")
            ]);
            return response()->json(['message' => 'Chapter Question stored successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function getChampionshipStatusAttribute()
    {
        if ($this->start_date > Carbon::now()) {
            return Championship::SCHEDULE;
        }elseif (($this->start_date <= Carbon::now()) && ($this->end_date >= Carbon::now())) {
            return Championship::LIVE;
        }else {
            return Championship::COMPLETED;
        }
    }
}
