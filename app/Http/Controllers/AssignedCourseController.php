<?php

namespace App\Http\Controllers;
// use App\Models\AssignedCourse;
use App\Models\User;
use App\Assignedcourse;
use App\Models\ScormPackage;
use App\ScormPackage as AppScormPackage;
use Illuminate\Http\Request;

class AssignedCourseController extends Controller
{
      public function create()
    {
        $users = User::all();
        $courses = AppScormPackage::all();

        return view('assigned_courses.create', compact('users', 'courses'));
    }

   public function getCourseExpireDate($courseId, Request $request)
{
    $userId = $request->query('user_id');

    // Check if already assigned
    $assigned = AssignedCourse::where('user_id', $userId)
        ->where('course_id', $courseId)
        ->first();

    if ($assigned) {
        return response()->json([
            'expire_date' => $assigned->expire_date ? $assigned->expire_date->format('Y-m-d H:i') : null
        ]);
    }

    // If not assigned, calculate using watch_time
    $course = AppScormPackage::findOrFail($courseId);
    $expireDate = now()->addMinutes($course->watch_time)->format('Y-m-d H:i');

    return response()->json(['expire_date' => $expireDate]);
}


 public function store(Request $request)
{
    $request->validate([
        'user_id'   => 'required|exists:users,id',
        'course_id' => 'required|exists:scorm_packages,id',
    ]);

    $exists = AssignedCourse::where('user_id', $request->user_id)
                ->where('course_id', $request->course_id)
                ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'This course is already assigned to this user!');
    }

    $course = AppScormPackage::findOrFail($request->course_id);
    $expireDate = now()->addMinutes($course->watch_time);

    AssignedCourse::create([
        'user_id'     => $request->user_id,
        'course_id'   => $request->course_id,
        'expire_date' => $expireDate,
        'enrolled_at' => now()
    ]);

    return redirect()->back()->with('success', 'Course successfully assigned to user!');
}



    public function index()
    {
        $assignedCourses = AssignedCourse::with(['user', 'course'])->get();
        return view('assigned_courses.index', compact('assignedCourses'));
    }
}
