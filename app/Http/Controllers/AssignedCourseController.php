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

    // Save karne ke liye
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:scorm_packages,id',
            'expire_date' => 'nullable|date'
        ]);
        AssignedCourse::create([

            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
            'expire_date' => $request->expire_date,
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
