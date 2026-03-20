<?php

namespace App\Http\Controllers;

use App\Subchapter;
use App\Models\Chapter;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Quiz\Question;
use App\Models\Quiz\UserAnswers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class FiltertQuestionController extends Controller
{
    

//    public function getChapterBySubject(Request $request)
// {
//     $token = session('token');

//     $response = Http::withHeaders([
//         'Authorization' => 'Bearer ' . $token
//     ])
//     ->withOptions([
//         'verify' => false  
//     ])
//     ->post('https://apexstg.datavoice.co.in/api/v1/get-chapterBy-subject', [
//         'subjects_id' => $request->subjects_id
//     ]);

//     return $response->json();
// }

 public function getChapterBySubject(Request $request)
    {
        /*"We retrieve all topics of a particular chapter. 
        In $idStatusOne, we have those topics with a status of 1. 
        In $idNoInc, we have those topics with a status of 0, 
        but only if there is at least one topic with a status of 1. 
        Therefore, if there is one topic with a status of 1 and another with a status of 0, we should not allow the creation of a subchapter, as one active topic is present."
        */

        if(isset($request->noSubjTopic)){
            $idStatusOne=[];
            $idNoInc=[];
            $id = $request->input('subjects_id');
            $idDetails = DB::table('topics')
                        ->where('topics.subject_id', $id)
                        ->whereNull('topics.subchapter_id')
                        ->select('topics.*')
                        ->get()
                        ->toArray();
            foreach($idDetails as $idDetail){
                if($idDetail->status == 1){
                    $idStatusOne[$idDetail->chapters_id][]=$idDetail->id;
                }
            }

            foreach($idDetails as $idDetail){
                foreach($idStatusOne as $key=>$value){
                    if($idDetail->chapters_id == $key){
                        $idNoInc[]=$idDetail->id;
                    }
                }
            }
            
            $chapters = Chapter::select('chapters.*')
                        // ->leftJoin('topics', 'chapters.id', '=', 'topics.chapters_id')
                        ->where('chapters.status', 1)
                        ->where('chapters.subject_id', $id)
                        // ->where(function ($query) use ($id) {
                        //     $query->where(function ($q) use ($id) {
                        //         $q->whereNotNull('topics.subchapter_id');
                        //     })
                        //     ->orWhere(function ($q) use ($id) {
                        //         $q->where('topics.status', 0);
                        //     })
                        //     ->orWhereNull('topics.chapters_id');
                        // })
                        // ->where(function ($query) use ($idNoInc) {
                        //     $query->whereNotIn('topics.id', $idNoInc)
                        //         ->orWhereNull('topics.id');
                        // })
                        ->distinct()
                        ->get();

            return response()->json($chapters);
        }
        $id = $request->input('subjects_id');
       // dd($id);
        $chapters = Chapter::where('subject_id', $id)
        
                             ->where('status', 1)
                            //  ->whereNull('folder_name')
                            //  ->whereNull('launch_file')
                             ->get();
       // dd($chapters);
        return response()->json($chapters);
    }


// public function getSubChapters(Request $request)
// {
//     $token = session('token');
// //dd($token);
//     $response = Http::withHeaders([
//         'Authorization' => 'Bearer ' . $token
//     ])
//         ->withOptions([
//         'verify' => false  
    
//     ])->post('https://apexstg.datavoice.co.in/api/v1/get-subChapterBy-chapter', [
//         'chapters_id' => $request->chapter_id
//     ]);

//     return $response->json();
// }

 public function getSubChapters(Request $request){
        $id = $request->input('chapters_id');
        $sub_chapters = Subchapter::where('chapter_id', $id)->where('status', Subject::ACTIVE)->get();
        return response()->json($sub_chapters);
    }

// public function (Request $request)
// {
//     $token = session('token');

//     $request->validate([
//         'subject_id' => 'required|integer',
//         'chapter_id' => 'required|integer',
//         'subchapter_id' => 'nullable|integer',
//         'difficult_level_id' => 'required|integer',
//         'used_status' => 'nullable|string',
//         'limit' => 'nullable|integer|min:1|max:500',
//     ]);

//     try {
//         $payload = [
//             'subject_id' => $request->subject_id,
//             'chapter_id' => $request->chapter_id,
//             'subchapter_id' => $request->subchapter_id,
//             'difficult_level_id' => $request->difficult_level_id,
//             'used_status' => $request->used_status,
//             'limit' => $request->limit ?? 50,
//         ];

//         $response = Http::withHeaders([
//             'Authorization' => 'Bearer ' . $token
//         ])
//         ->withOptions([
//             'verify' => false   // 
//         ])
//         ->post('https://apexstg.datavoice.co.in/api/v1/qbundle/filter', $payload);

//         return $response->json();

//     } catch (\Exception $e) {

//         return response()->json([
//             'status' => false,
//             'message' => 'Server error',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

public function filterQBundle(Request $request)
{
    // dd($request->all());
    try {

        $request->validate([
            'subject_id' => 'required|integer',
            'chapter_id' => 'required|integer',
            'subchapter_id' => 'nullable|integer',
            'difficult_level_id' => 'required|integer',
            'used_status' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:500',
        ]);

        $limit = $request->limit ?? 50;

        $query = Question::with([
            'answers:id,question_id,answer,correctans,option_image,image_flag',
            'subject:id,name',
            'chapter:id,name',
            'subchapter:id,name',
            'answerType:id,name',
            'difficultLevel:id,name',
            // 'userAttempts:id,question_id',

            'solution:id,question_id,name',
        ]);

        $query->where('subject_id', $request->subject_id);

        if ($request->chapter_id) {
            $query->where('chapter_id', $request->chapter_id);
        }

        if ($request->subchapter_id) {
            $query->where('subchapter_id', $request->subchapter_id);
        }

        if ($request->difficult_level_id) {
            $query->where('difficult_levels_id', $request->difficult_level_id);
        }

        if ($request->used_status === 'used') {
            $query->whereHas('userAttempts');
        }

        if ($request->used_status === 'not_used') {
            $query->whereDoesntHave('userAttempts');
        }

        $questions = $query->paginate($limit);

        $data = collect($questions->items())->map(function ($q) {
            return [
                "id" => $q->id,
                "question" => $q->question,

                // ✅ NEW: solution text
                "solution_text" => optional($q->solution)->name,

                "difficult_levels_id" => $q->difficult_levels_id,

                "difficult_level" => [
                    "id" => optional($q->difficultLevel)->id,
                    "name" => optional($q->difficultLevel)->name
                ],

                "answers" => $q->answers->map(function ($a) {
                    return [
                        "id" => $a->id,
                        "answer" => $a->answer,
                        "correctans" => $a->correctans,
                    ];
                }),

                "answer_type" => [
                    "id" => optional($q->answerType)->id,
                    "name" => optional($q->answerType)->name
                ],

                "subject" => [
                    "id" => optional($q->subject)->id,
                    "name" => optional($q->subject)->name
                ],

                "chapter" => [
                    "id" => optional($q->chapter)->id,
                    "name" => optional($q->chapter)->name
                ],

                "subchapter" => [
                    "id" => optional($q->subchapter)->id,
                    "name" => optional($q->subchapter)->name
                ],

                "used" => $q->userAttempts->count() > 0,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'total' => $questions->total(),
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}

public function modePage($id)
{
    return view('coursemode', ['courseId' => $id]);
}

public function store(Request $request)
{
    $userId = auth()->id();

    foreach ($request->data as $item) {

        $question = Question::findOrFail($item['question_id']);

        $correctAnswerId = $question->correct_answers_id;
        $isCorrect = ($item['answers_id'] == $correctAnswerId);

        UserAnswers::create([

            'user_id' => $userId,
            'question_id' => $item['question_id'],
            'answers_id' => $item['answers_id'],
            'correct_answers_id' => $correctAnswerId,

            'is_correct' => $isCorrect,
            'marks' => $isCorrect ? 1 : 0,
            'negative_marks' => $isCorrect ? 0 : 0,
            'message' => $isCorrect ? 'Correct Answer' : 'Wrong Answer',

            'time_taken' => $item['time_taken'] ?? 0,
            'user_question_status' => $item['user_question_status'],
            'is_cumulative_question' => $item['is_cumulative_question'] ?? false,

            'chapters_questions_id' => null,
            'submitted_quiz_id' => null,
            'submitted_objective_id' => null,
            'submitted_block_id' => null,
            'submitted_championship_id' => null,
            'submitted_tournament_id' => null,
            'user_test_id' => null,
            'user_question_id' => null,
            'option_id' => null,

            'esec' => null,
            'rsec' => null,
            'mil' => null,
            'status' => 1,

            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => 'User answers saved successfully'
    ]);
}



}
