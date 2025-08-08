<?php

namespace App\Http\Controllers;
use App\Models\QuizAttempt;
use App\ScormPackage as AppScormPackage;
use App\CourseProgress as AppCourseProgress;
use Illuminate\Http\Request;
use App\Models\CourseProgress;
use App\QuizAttempt as AppQuizAttempt;

class CourseProgressController extends Controller
{
public function save(Request $request)
{
    $request->validate([
        'course_id' => 'required|integer',
        'session_time' => 'nullable|integer',
        'progress_percent' => 'nullable|integer',
        'cmi_core_lesson_location' => 'nullable|string',
        'cmi_core_lesson_status' => 'nullable|string',
        'suspend_data' => 'nullable|string', // ✅ Validate suspend_data
    ]);

    $userId = auth()->id();

    $progress = AppCourseProgress::firstOrNew([
        'user_id' => $userId,
        'course_id' => $request->course_id,
    ]);

    // ⏱️ Session time logic
    $oldTime = $progress->session_time ?? 0;
    $newTime = $request->session_time ?? 0;
    $totalSessionTime = max($oldTime, $newTime);

    // 📘 Course duration
    $course = AppScormPackage::find($request->course_id);
    $totalDuration = $course->duration_in_seconds ?? 0;

    // ✅ Lesson status
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

    // ✅ Store suspend_data in progress_data JSON column
    $existingProgressData = $progress->progress_data ?? [];
    $existingProgressData['suspend_data'] = $request->suspend_data ?? null;
    $progress->progress_data = $existingProgressData;

    $progress->save();

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
    $userId = auth()->id();

    foreach ($request->results as $result) {
        $rawNewQuestions = $result['question_ids'] ?? [];

        // Group questions by their __ID (attempt number)
        $groupedByAttemptId = [];

        foreach ($rawNewQuestions as $q) {
            if (preg_match('/__(\d+)$/', $q, $matches)) {
                $attemptId = (int)$matches[1];
                
                //  If 0, treat it as 1
                $attemptId = $attemptId === 0 ? 1 : $attemptId;

                $groupedByAttemptId[$attemptId][] = $q;
            }
        }

        // Loop through each group (based on __ID at end)
        foreach ($groupedByAttemptId as $attemptNumber => $questionsInThisAttempt) {
            $existingAttempt = AppQuizAttempt::where('user_id', $userId)
                ->where('quiz_name', $request->quiz_name)
                ->where('chapter_name', $result['chapter_name'])
                ->where('attempt_number', $attemptNumber)
                ->first();

            $totalQuestions = count($questionsInThisAttempt);
            $correct = $result['correct_answers'] ?? 0;
            $wrong = $result['wrong_answers'] ?? 0;

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
                //  Create new
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
        }
    }

    return response()->json(['status' => 'success']);
}



public function getAttempts(Request $request)
{
    $userId = auth()->id();
    $quizName = $request->quiz_name;

    $attempts = AppQuizAttempt::where('user_id', $userId)
        ->where('quiz_name', $quizName)
        ->orderBy('attempt_number')
        ->get(['attempt_number', 'chapter_name', 'score_percent']);
//dd($attempts->all());
    return response()->json($attempts);
}




}
