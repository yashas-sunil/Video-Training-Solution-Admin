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

        $assignedCourses = AssignedCourse::with([
            'course' => function ($query) {
                $query->where('status', 1);
            },
            'progress' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
            'courseView' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }
        ])
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();

        $expiredCoursesCount = 0;
        $totalCourses = 0;
        $completedCoursesCount = 0;
        $inProgressCount = 0;
        $totalWatchTime = 0;
        $now = \Carbon\Carbon::now();

        $coursesWithProgress = $assignedCourses->map(function ($assigned) use (
            &$completedCoursesCount,
            &$inProgressCount,
            &$totalWatchTime,
            &$expiredCoursesCount,
            &$totalCourses,
            $now
        ) {
            $progressRecords = $assigned->progress;
            $courseView = $assigned->courseView->first();
            $course = $assigned->course;

            if (!$course) {
                return null;
            }

            $totalCourses++;

            $courseWatchTime = $progressRecords->sum('session_time');
            $totalWatchTime += $courseWatchTime;

            $isExpired = false;
            if ($assigned->expire_date && \Carbon\Carbon::parse($assigned->expire_date)->lt($now)) {
                $isExpired = true;
            }
            if ($courseView && $course->view_limit && $courseView->view_limit > $course->view_limit) {
                $isExpired = true;
            }

            if ($isExpired) {
                $expiredCoursesCount++;
            }

            if ($progressRecords->isNotEmpty()) {
                if ($progressRecords->where('cmi_core_lesson_status', '!=', 'completed')->isEmpty()) {
                    $completedCoursesCount++;
                } elseif (!$isExpired) {
                    $inProgressCount++;
                }
            }

            return [
                'course' => $course,
                'progress' => $progressRecords,
                'view' => $courseView,
                'total_session_time' => $courseWatchTime,
                'is_expired' => $isExpired,
                'is_disabled' => false,
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
