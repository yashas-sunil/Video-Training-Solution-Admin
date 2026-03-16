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

    public function dashboard(Request $request)
    {
        // Get filter type and selected value - only use defaults if parameters exist
        $filterType = $request->get('filter_type'); // 'test' or 'course' - no default
        $filterId = $request->get('filter_id');
        
        // Get all tests and courses (from scorm_packages table)
        $tests = \App\admin_test::all();
        $packages = DB::table('scorm_packages')->select('id', 'title as name')->get();
        
        $leaderboard = [];
        
        if ($filterType === 'test' && $filterId) {
            // Filter by test - show all students who attempted this test
            $leaderboard = DB::table('user_test_results')
                ->selectRaw('user_id, test_id, SUM(is_correct) as correct_answers, COUNT(*) as total_questions')
                ->where('test_id', $filterId)
                ->groupBy('user_id', 'test_id')
                ->orderByDesc('correct_answers')
                ->get();
                
            // Add user and test details
            $leaderboard = $leaderboard->map(function($result) {
                $user = \App\Models\User::find($result->user_id);
                $test = \App\admin_test::find($result->test_id);
                return (object) [
                    'user_id' => $result->user_id,
                    'user_name' => $user ? $user->name : 'Unknown',
                    'test_id' => $result->test_id,
                    'test_name' => $test ? $test->name : 'Unknown',
                    'correct_answers' => $result->correct_answers,
                    'total_questions' => $result->total_questions,
                    'percentage' => $result->total_questions > 0 ? round(($result->correct_answers / $result->total_questions) * 100, 2) : 0,
                ];
            });
        } 
        elseif ($filterType === 'course' && $filterId) {
            // Filter by course - show students in course with their best test scores
            $bestScores = DB::table('user_test_results as utr')
                ->join('admin_test as at', 'utr.test_id', '=', 'at.id')
                ->selectRaw('utr.user_id, utr.test_id, SUM(utr.is_correct) as correct_answers, COUNT(utr.id) as total_questions')
                ->where('at.course_id', $filterId)
                ->groupBy('utr.user_id', 'utr.test_id')
                ->get();
            
            // Get unique students with their best score
            $uniqueScores = [];
            foreach ($bestScores as $score) {
                if (!isset($uniqueScores[$score->user_id]) || $uniqueScores[$score->user_id]->correct_answers < $score->correct_answers) {
                    $uniqueScores[$score->user_id] = $score;
                }
            }
            
            $courseId = intval($filterId);
            $leaderboard = collect($uniqueScores)
                ->sortByDesc('correct_answers')
                ->values()
                ->map(function($result) use ($courseId) {
                    $user = \App\Models\User::find($result->user_id);
                    $package = DB::table('scorm_packages')->where('id', $courseId)->first();
                    $test = \App\admin_test::find($result->test_id);
                    return (object) [
                        'user_id' => $result->user_id,
                        'user_name' => $user ? $user->name : 'Unknown',
                        'course_id' => $courseId,
                        'course_name' => $package ? $package->title : 'Unknown',
                        'test_id' => $result->test_id,
                        'test_name' => $test ? $test->test_name : 'Unknown',
                        'correct_answers' => $result->correct_answers,
                        'total_questions' => $result->total_questions,
                        'percentage' => $result->total_questions > 0 ? round(($result->correct_answers / $result->total_questions) * 100, 2) : 0,
                    ];
                });
        }
        
        return view('admin.testResults', compact('leaderboard', 'tests', 'packages', 'filterType', 'filterId'));
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
