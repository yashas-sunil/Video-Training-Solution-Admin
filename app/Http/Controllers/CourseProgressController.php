<?php

namespace App\Http\Controllers;
use App\Models\QuizAttempt;
use App\ScormPackage as AppScormPackage;
use App\CourseProgress as AppCourseProgress;
use Illuminate\Http\Request;
use App\Models\CourseProgress;
use App\QuizAttempt as AppQuizAttempt;
use App\Models\AppQuizAttemptAnswer;
use App\QuizAttemptAnswer;

class CourseProgressController extends Controller
{
public function save(Request $request)
{
  //  dd($request->all());
    $request->validate([
        'course_id' => 'required|integer',
        'session_time' => 'nullable|integer',
        'progress_percent' => 'nullable|integer',
        'cmi_core_lesson_location' => 'nullable|string',
        'cmi_core_lesson_status' => 'nullable|string',
        'suspend_data' => 'nullable|string', 
    ]);

    $userId = auth()->id();

    $progress = AppCourseProgress::firstOrNew([
        'user_id' => $userId,
        'course_id' => $request->course_id,
    ]);

    //  Session time logic
    $oldTime = $progress->session_time ?? 0;
    $newTime = $request->session_time ?? 0;
    $totalSessionTime = max($oldTime, $newTime);
//dd($newTime->all());
    //  Course duration
    $course = AppScormPackage::find($request->course_id);
    $totalDuration = $course->duration_in_seconds ?? 0;

    //  Lesson status
    $status = $request->cmi_core_lesson_status ?? $progress->cmi_core_lesson_status;
    if ($totalDuration > 0 && $totalSessionTime >= $totalDuration) {
        $status = 'completed';
    }

    // 💾 Save core fields
    $progress->session_time = $totalSessionTime;
    $progress->resume_from_time = $totalSessionTime;
    $progress->cmi_core_lesson_location = $request->cmi_core_lesson_location ?? $progress->cmi_core_lesson_location;
    $progress->cmi_core_lesson_status = $status;
    $progress->progress_percent = $request->progress_percent ?? $progress->progress_percent ?? 0;
    $progress->last_watched_at = now();

    //  Store suspend_data in progress_data JSON column
    $existingProgressData = $progress->progress_data ?? [];
    $existingProgressData['suspend_data'] = $request->suspend_data ?? null;
    $progress->progress_data = $existingProgressData;
// dd($progress->all());
    $progress->save();
   //dd("hello");
   // dd($progress->toArray());

    return response()->json(['status' => 'success']);
}
public function get($id)
{
    $userId = auth()->id();

    $progress = AppCourseProgress::where('user_id', $userId)
        ->where('course_id', $id)
        ->first();

    return response()->json([
        'scroll_position' => $progress->scroll_position ?? 0
    ]);
}

public function store(Request $request)
{
  //  dd($request->all());
    $userId = auth()->id();

    foreach ($request->results as $result) {
        $rawNewQuestions = $result['question_ids'] ?? [];

        // Group questions by attempt number
        $groupedByAttemptId = [];
        foreach ($rawNewQuestions as $q) {
            if (preg_match('/__(\d+)$/', $q, $matches)) {
                $attemptId = (int)$matches[1];
                $attemptId = $attemptId === 0 ? 1 : $attemptId;
                $groupedByAttemptId[$attemptId][] = $q;
            }
        }

        foreach ($groupedByAttemptId as $attemptNumber => $questionsInThisAttempt) {
            $existingAttempt = AppQuizAttempt::where('user_id', $userId)
                ->where('quiz_name', $request->quiz_name)
                ->where('chapter_name', $result['chapter_name'])
                ->where('attempt_number', $attemptNumber)
                ->first();

            $totalQuestions = count($questionsInThisAttempt);
            $correct = $result['correct_answers'] ?? 0;

            $score = $totalQuestions > 0
                ? round(($correct / ($result['total_questions'] ?? $totalQuestions)) * $totalQuestions)
                : 0;

            $wrongScaled = $totalQuestions - $score;

            if ($existingAttempt) {
                $existingAttempt->total_questions += $totalQuestions;
                $existingAttempt->correct_answers += $score;
                $existingAttempt->wrong_answers += $wrongScaled;
                $existingAttempt->score_percent = $existingAttempt->total_questions > 0
                    ? round(($existingAttempt->correct_answers / $existingAttempt->total_questions) * 100)
                    : 0;

                $existingRaw = json_decode($existingAttempt->question_ids, true) ?? [];
                $existingAttempt->question_ids = json_encode(array_unique(array_merge($existingRaw, $questionsInThisAttempt)));
                $existingAttempt->save();
            } else {
                $newAttempt = new AppQuizAttempt();
                $newAttempt->user_id = $userId;
                $newAttempt->quiz_name = $request->quiz_name;
                $newAttempt->chapter_name = $result['chapter_name'];
                $newAttempt->attempt_number = $attemptNumber;
                $newAttempt->total_questions = $totalQuestions;
                $newAttempt->correct_answers = $score;
                $newAttempt->wrong_answers = $wrongScaled;
                $newAttempt->score_percent = $totalQuestions > 0
                    ? round(($score / $totalQuestions) * 100)
                    : 0;
                $newAttempt->question_ids = json_encode($questionsInThisAttempt);
                $newAttempt->save();
            }

           foreach ($questionsInThisAttempt as $questionId) {
    $attemptNumberFromId = 1; // Default = 1

    if (preg_match('/_+(\d+)$/', $questionId, $matches)) {
        $attemptNumberFromId = (int)$matches[1];

        if ($attemptNumberFromId === 0) {
            $attemptNumberFromId = 1;
        }
    }

    $studentAnswer = null;
    $correctAnswer = null;
    $isCorrect = false;

    if (!empty($result['student_answers'])) {
        foreach ($result['student_answers'] as $ans) {
            if ($ans['question_id'] === $questionId) {
                $studentAnswer = $ans['student_answer'] ?? null;
                $correctAnswer = $ans['correct_answer'] ?? null;
                $isCorrect = ($studentAnswer === $correctAnswer);
                break;
            }
        }
    }

    QuizAttemptAnswer::create([
        'user_id'        => $userId,
        'quiz_name'      => $request->quiz_name,
        'chapter_name'   => $result['chapter_name'],
        'attempt_number' => $attemptNumberFromId, 
        'question_id'    => $questionId,
        'user_answer'    => $studentAnswer,
        'correct_answer' => $correctAnswer,
        'is_correct'     => $isCorrect,
    ]);
}

        }
    }

    return response()->json(['status' => 'success']);
}




public function getAttempts(Request $request)
{
    //dd($request->all());
    $quizName = $request->query('quiz_name');
    $userId   = auth()->id();

    // Pehle attempts fetch karo
    $attempts = AppQuizAttempt::where('user_id', $userId)
        ->where('quiz_name', $quizName)
        ->orderByDesc('attempt_number')
        ->get();

    $data = $attempts->map(function ($attempt) use ($userId, $quizName) {

        // Is attempt ke saare answers le aao
        $answers = QuizAttemptAnswer::where('user_id', $userId)
            ->where('quiz_name', $quizName)
            ->where('chapter_name', $attempt->chapter_name)
            ->where('attempt_number', $attempt->attempt_number)
            ->get();

        $questionsData = $answers->map(function ($ans) {
            // Question ID clean karna
            $cleanId = preg_replace('/___\d+$/', '', $ans->question_id); // Attempt suffix remove
            $cleanId = str_replace('_', ' ', $cleanId); // underscores → spaces

            // Compare lowercased & trimmed answers
            $isCorrect = false;
            if (!empty($ans->correct_answer) && !empty($ans->user_answer)) {
                $isCorrect = trim(strtolower($ans->user_answer)) === trim(strtolower($ans->correct_answer));
            }

            return [
                'question_id'    => $cleanId,
                'user_answer'    => $ans->user_answer ?? '',
                'correct_answer' => $ans->correct_answer ?? '', 
                'is_correct'     => $isCorrect
            ];
        });

        return [
            'attempt_number' => $attempt->attempt_number,
            'chapter_name'   => $attempt->chapter_name,
            'score_percent'  => $attempt->score_percent,
            'questions'      => $questionsData
        ];
    });

    return response()->json($data);
}



}
