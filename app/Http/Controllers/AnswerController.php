<?php

namespace App\Http\Controllers;

use App\UserTestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    public function fetchSolutionByQuestions(Request $request)
    {
        // Minimal stub to avoid route resolution errors.
        return response()->json(['message' => 'OK', 'data' => []]);
    }

    public function fetchSolutionByQuestionsAlt(Request $request)
    {
        // Some routes reference the same method name; provide an alternate.
        return response()->json(['message' => 'OK', 'data' => []]);
    }
     public function saveAnswers(Request $request)
    {
        $request->validate([
            'test_id'                         => 'required|integer',
            'mode'                            => 'required|string',
            'questions'                       => 'required|array|min:1',
            'questions.*.question_id'         => 'required|integer',
            'questions.*.selected_option_id'  => 'nullable|integer',
            'questions.*.is_correct'          => 'required|integer|in:0,1',
        ]);
// dd( $request->all());
        $userId    = Auth::id();          
        $testId    = $request->test_id;
        $mode      = $request->mode;
        $questions = $request->questions;
//dd($userId, $testId, $mode, $questions);
        DB::beginTransaction();

        try {

            UserTestResult::where('user_id', $userId)
                          ->where('test_id', $testId)
                          ->delete();

            $insertData = [];

            foreach ($questions as $q) {
                $insertData[] = [
                    'user_id'            => $userId,
                    'test_id'            => $testId,
                    'question_id'        => $q['question_id'],
                    'selected_option_id' => $q['selected_option_id'] ?? null,
                    'is_correct'         => $q['is_correct'],
                    'mode'               => $mode,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }

            UserTestResult::insert($insertData);

            $totalQuestions = count($questions);
            $correctCount   = collect($questions)->where('is_correct', 1)->count();
            $wrongCount     = $totalQuestions - $correctCount;
            $percentage     = $totalQuestions > 0
                                ? round(($correctCount / $totalQuestions) * 100, 2)
                                : 0;

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Answers saved successfully',
                'result'  => [
                    'test_id'    => $testId,
                    'user_id'    => $userId,
                    'total'      => $totalQuestions,
                    'correct'    => $correctCount,
                    'wrong'      => $wrongCount,
                    'percentage' => $percentage,
                    'mode'       => $mode,
                ]
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Failed to save answers',
                'error'   => $e->getMessage()
            ], 500);

        }

    }// saveAnswers()

}
