<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Question;
use App\QuestionBank;
use App\ScormPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseQuestionController extends Controller
{
      public function index()
{
    $courses = ScormPackage::all();
    $banks   = QuestionBank::all();
    $levels  = DB::table('difficult_levels')->get();

    return view('course_questions.index', compact('courses', 'banks','levels'));
}

    // Filter Questions (Already Assigned Hide)
   public function filter(Request $request)
{
    $courseId = $request->course_id;
    $bankId = $request->bank_id;
    $assignedIds = DB::table('course_question')
        ->where('course_id', $courseId)
        ->pluck('question_id');

    $questions = Question::where('question_banks_id', $bankId)
        ->whereNotIn('id', $assignedIds)
        ->with('difficultLevel')
        ->get();

    return view('course_questions.partials.question_list', compact('questions'))->render();
}


// Assign Questions
public function assign(Request $request)
{
  //  dd($request->all());
    $courseId = $request->course_id;
    $subjectId = $request->subject_id;
    $questionIds = $request->question_ids;

    foreach ($questionIds as $questionId) {

        DB::table('course_question')->updateOrInsert(
            [
                'course_id' => $courseId,
                'question_id' => $questionId
            ],
            [
               'subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    return response()->json([
        'success' => true,
        'message' => 'Questions Assigned Successfully'
    ]);
}

  public function getAllQuestions(Request $request)
{
    $bankId = $request->bank_id;
    $difficulty = $request->difficulty;

    $questions = Question::where('bank_id', $bankId);

    if($difficulty){
        $questions->where('difficult_levels_id',$difficulty);
    }

    $ids = $questions->pluck('id');

    return response()->json($ids);
}
}
