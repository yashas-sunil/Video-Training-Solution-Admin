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
        'cmi_core_lesson_location' => 'nullable|string',
        'cmi_core_lesson_status' => 'nullable|string'
    ]);

    $userId = auth()->id();

    // Get or create progress
    $progress = AppCourseProgress::firstOrNew([
        'user_id' => $userId,
        'course_id' => $request->course_id,
    ]);

    $oldTime = $progress->session_time ?? 0;
    $newTime = $request->session_time ?? 0;
    $totalSessionTime = $oldTime + $newTime;
    // Get course duration
    $course = AppScormPackage::find($request->course_id);
    $totalDuration = ($course && $course->duration_in_seconds) ? $course->duration_in_seconds : 0;
    // Set lesson status
    $lessonStatus = $request->cmi_core_lesson_status ?? $progress->cmi_core_lesson_status;
    if ($totalDuration > 0 && $totalSessionTime >= $totalDuration) {
        $lessonStatus = 'completed';
    }
    $progress->session_time = $totalSessionTime;
    $progress->cmi_core_lesson_location = $request->cmi_core_lesson_location ?? $progress->cmi_core_lesson_location;
    $progress->cmi_core_lesson_status = $lessonStatus;
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
