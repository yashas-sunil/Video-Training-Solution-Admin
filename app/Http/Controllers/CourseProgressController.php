<?php

namespace App\Http\Controllers;

use App\ScormPackage as AppScormPackage;
use App\CourseProgress as AppCourseProgress;
use Illuminate\Http\Request;
use App\Models\CourseProgress;
class CourseProgressController extends Controller
{
public function save(Request $request)
{
    
    $request->validate([
        'course_id' => 'required|integer',
        'session_time' => 'nullable|integer',
        'progress_percent' => 'nullable|integer',
        'cmi_core_lesson_location' => 'nullable|string',
        'cmi_core_lesson_status' => 'nullable|string'
    ]);
//dd($request->all());
    $userId = auth()->id();

    $progress = AppCourseProgress::firstOrNew([
        'user_id' => $userId,
        'course_id' => $request->course_id,
    ]);

    // Session time update logic
    $oldTime = $progress->session_time ?? 0;
    $newTime = $request->session_time ?? 0;
    $totalSessionTime = max($oldTime, $newTime); // always keep max time

    // Course duration
    $course = AppScormPackage::find($request->course_id);
    $totalDuration = $course->duration_in_seconds ?? 0;

    // Lesson status logic
    $status = $request->cmi_core_lesson_status ?? $progress->cmi_core_lesson_status;
    if ($totalDuration > 0 && $totalSessionTime >= $totalDuration) {
        $status = 'completed';
    }

    $progress->session_time = $totalSessionTime;
    $progress->resume_from_time = $totalSessionTime; // Save resume point in seconds
    $progress->cmi_core_lesson_location = $request->cmi_core_lesson_location ?? $progress->cmi_core_lesson_location;
    $progress->cmi_core_lesson_status = $status;
    $progress->progress_percent = $request->progress_percent ?? $progress->progress_percent ?? 0;
    $progress->last_watched_at = now();
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

}
