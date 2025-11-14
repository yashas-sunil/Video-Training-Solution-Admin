<?php

namespace App\Http\Controllers;

use App\Assignedcourse;
use App\CourseProgress;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $assignedCourses = \DB::table('user_courses')
            ->join('admin_courses', 'user_courses.course_id', '=', 'admin_courses.id')
            ->leftJoin('scorm_packages', 'admin_courses.scorm_package_id', '=', 'scorm_packages.id') // join SCORM table
            ->where('user_courses.user_id', $userId)
            ->select(
                'admin_courses.id as course_id',
                'admin_courses.title',
                'admin_courses.description',
                // 'admin_courses.access_password',
                'user_courses.expire_date',
                'scorm_packages.folder_name',
                'scorm_packages.launch_file'
            )
            ->get();

        // Create the full iframe URL
        foreach ($assignedCourses as $course) {
            if ($course->folder_name && $course->launch_file) {
                $course->training_link = asset('scorm_packages/' . $course->folder_name . '/' . $course->launch_file);
            } else {
                $course->training_link = null;
            }
        }

        return view('user.dashboard', compact('assignedCourses'));
    }


    public function resumeupdate(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:admin_courses,id',
            'progress_percent' => 'required|numeric|min:0|max:100',
        ]);

        $progress = CourseProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'course_id' => $request->course_id],
            ['progress_percent' => $request->progress_percent, 'last_accessed_at' => now()]
        );

        return response()->json(['status' => 'success']);
    }

    public function userindex()
    {
        $userId = auth()->id();
        $now = \Carbon\Carbon::now();

        $assignedCourses = AssignedCourse::with([
            'course' => fn($query) => $query->where('status', 1),
            'progress' => fn($query) => $query->where('user_id', $userId),
            'courseView' => fn($query) => $query->where('user_id', $userId),
        ])
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();

        $completedCoursesCount = 0;
        $inProgressCount = 0;
        $expiredCoursesCount = 0;
        $totalCourses = 0;
        $totalWatchTime = 0;

        $coursesWithProgress = $assignedCourses->map(function ($assigned) use (
            &$completedCoursesCount,
            &$inProgressCount,
            &$expiredCoursesCount,
            &$totalCourses,
            &$totalWatchTime,
            $now
        ) {
            $course = $assigned->course;
            if (!$course) return null;

            $progressRecords = $assigned->progress;
            $courseView = $assigned->courseView->first();
            $totalCourses++;

            $masterLimit = $course->view_limit ?? 1;
            $userViewed = $courseView->view_limit ?? 0;
            $currentAttempt = min($userViewed, $masterLimit);

            $courseDuration = $course->watch_time ? $course->watch_time * 60 : 0;
            $totalWatched = $progressRecords->sum('session_time');
            $watchedThisAttempt = max(0, $totalWatched - ($currentAttempt - 1) * $courseDuration);
            $watchedThisAttempt = min($watchedThisAttempt, $courseDuration);
            $totalWatchTime += $totalWatched;

            $isCompleted = $progressRecords->whereIn('cmi_core_lesson_status', ['completed', 'passed'])->isNotEmpty();

            if ($isCompleted) {
                $progressPercent = 100;
                $completedCoursesCount++;
            } elseif ($progressRecords->isNotEmpty()) {
                $progressPercent = $courseDuration > 0 ? round(($watchedThisAttempt / $courseDuration) * 100, 2) : 0;

                if ($progressPercent > 80) {
                    $progressPercent = 80;
                }

                $inProgressCount++;
            } else {
                $progressPercent = 0;
            }

            $isExpired = false;
            if (!$isCompleted) {
                if ($assigned->expire_date && \Carbon\Carbon::parse($assigned->expire_date)->lt($now)) $isExpired = true;
                if ($userViewed > $masterLimit) $isExpired = true;
                if ($isExpired) $expiredCoursesCount++;
            }

            $isDisabled = $course->status == 0 || false;

            return [
                'course' => $course,
                'progress' => $progressRecords,
                'view' => $courseView,
                'display_view_limit' => $currentAttempt,
                'total_session_time' => $totalWatched,
                'watched_this_attempt' => $watchedThisAttempt,
                'is_expired' => $isExpired,
                'is_disabled' => $isDisabled,
                'is_completed' => $isCompleted,
                'progress_percent' => $progressPercent,
                'master_limit' => $masterLimit,
            ];
        })->filter(fn($item) => !is_null($item));

        return view('user.dashboard', [
            'courses' => $coursesWithProgress,
            'completedCourses' => $completedCoursesCount,
            'inProgressCount' => $inProgressCount,
            'totalCourses' => $totalCourses,
            'totalWatchTime' => $totalWatchTime,
            'expiredCoursesCount' => $expiredCoursesCount,
        ]);
    }
}
