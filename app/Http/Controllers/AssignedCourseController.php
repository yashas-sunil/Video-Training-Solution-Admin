<?php

namespace App\Http\Controllers;
// use App\Models\AssignedCourse;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Assignedcourse;
use App\CourseView;
use App\Models\Course;
use Carbon\Carbon;
use App\Models\ScormPackage;
use App\ScormPackage as AppScormPackage;
use Illuminate\Http\Request;

class AssignedCourseController extends Controller
{
    public function create()
    {
        $users = User::all();

        $courses = AppScormPackage::where('status', 1)->get();

        return view('assigned_courses.create', compact('users', 'courses'));
    }

    public function getCourseExpireDate($courseId, Request $request)
    {
        $userId = $request->query('user_id');

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
            'user_id'      => 'required|exists:users,id',
            'course_id'    => 'required|exists:scorm_packages,id',
            'expire_date'  => 'nullable|string',
        ]);

        $exists = AssignedCourse::where('user_id', $request->user_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This course is already assigned to this user!');
        }

        $course = AppScormPackage::findOrFail($request->course_id);

        $expireDate = $request->expire_date
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->expire_date)->format('Y-m-d H:i:s')
            : now()->addMinutes($course->watch_time)->format('Y-m-d H:i:s');
        //dd($expireDate);
        AssignedCourse::create([
            'user_id'     => $request->user_id,
            'course_id'   => $request->course_id,
            'expire_date' => $expireDate,
            'enrolled_at' => now(),
        ]);

        return redirect()
            ->route('assigned-courses.index')
            ->with('success', 'Course successfully assigned to user!');
    }
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $query = AssignedCourse::with(['user', 'course'])
                ->select('assigned_courses.*');

            return DataTables::of($query)
                ->addColumn('user_name', fn($assign) => $assign->user->name ?? 'N/A')
                ->addColumn('course_title', fn($assign) => $assign->course->title ?? 'N/A')
                ->addColumn('view_limit', function ($assign) {
                    $view = CourseView::where('user_id', $assign->user_id)
                        ->where('course_id', $assign->course_id)
                        ->first();
                    return $view
                        ? "<span class='badge badge-info'>{$view->view_limit}</span>"
                        : "<span class='badge badge-secondary'>0</span>";
                })
                ->editColumn('enrolled_at', fn($assign) =>
                $assign->enrolled_at ? Carbon::parse($assign->enrolled_at)->format('d-m-Y') : '-')
                ->editColumn('expire_date', fn($assign) =>
                $assign->expire_date ? Carbon::parse($assign->expire_date)->format('d-m-Y H:i') : 'No Expiry')


                //  Bigger Toggle Icons
                // ->addColumn('status', function ($assign) {
                //     $icon = $assign->status
                //         ? "<i class='fas fa-toggle-on text-success' style='font-size:30px; cursor:pointer;' title='Active'></i>"
                //         : "<i class='fas fa-toggle-off text-secondary' style='font-size:30px; cursor:pointer;' title='Inactive'></i>";

                //     return "<span class='status-toggle' data-id='{$assign->id}' data-status='{$assign->status}'>$icon</span>";
                // })

                ->addColumn('status', function ($assign) {
                    $checked = $assign->status ? 'checked' : '';

                    return '
                    <div class="action-buttons">
                        <label class="switch">
                            <input type="checkbox" class="toggle-status" data-id="' . $assign->id . '" ' . $checked . '>
                            <span class="slider"></span>
                        </label>
                    </div>
                ';
                })


                //  Bigger Edit Button Icon
                ->addColumn('action', function ($assign) {
                    $editUrl = route('assigned-courses.edit', $assign->id);

                    return "
                    <a href='{$editUrl}' title='Edit' 
                       style='padding:6px 10px; border-radius:6px;'>
                        <i class='fas fa-edit text-primary fa-lg' style='font-size:22px;'></i>
                    </a>
                ";
                })
                ->rawColumns(['view_limit', 'status', 'action'])
                ->make(true);
        }

        $html = $builder->columns([
            ['data' => 'user_name', 'name' => 'user.name', 'title' => 'User'],
            ['data' => 'course_title', 'name' => 'course.title', 'title' => 'Course'],
            ['data' => 'view_limit', 'name' => 'view_limit', 'title' => 'Usage Count'],
            ['data' => 'enrolled_at', 'name' => 'enrolled_at', 'title' => 'Enrolled At'],
            ['data' => 'expire_date', 'name' => 'expire_date', 'title' => 'Expire Date'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ]);

        return view('assigned_courses.index', compact('html'));
    }


    public function edit($id)
    {
        $assignedCourse = AssignedCourse::findOrFail($id);
        $users = User::all();
        $courses = AppScormPackage::all();

        return view('assigned_courses.edit', compact('assignedCourse', 'users', 'courses'));
    }
    public function update(Request $request, $id)
    {
        $assignedCourse = AssignedCourse::findOrFail($id);

        $request->validate([
            'user_id' => 'required',
            'course_id' => 'required',
            'expire_date' => 'nullable|date',
        ]);

        $assignedCourse->update([
            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
            'expire_date' => $request->expire_date,
        ]);

        return redirect()->route('assigned-courses.index')->with('success', 'Assigned Course updated successfully!');
    }
    public function destroy($id)
    {
        $assignedCourse = AssignedCourse::findOrFail($id);
        $assignedCourse->delete();

        return redirect()->route('assigned-courses.index')->with('success', 'Assigned Course deleted successfully!');
    }
    public function toggleStatus($id)
    {
        $assign = AssignedCourse::findOrFail($id);
        $assign->status = !$assign->status;
        $assign->save();

        return response()->json([
            'success' => true,
            'message' => $assign->status ? 'Course enabled successfully!' : 'Course disabled successfully!'
        ]);
    }
}
