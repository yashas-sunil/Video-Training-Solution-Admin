<?php

namespace App\Http\Controllers;

use App\AdminCourse;
use App\ScormPackage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;


class AdminCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $query = ScormPackage::select(['id', 'title', 'watch_time', 'view_limit', 'status', 'created_at']);

            return DataTables::of($query)
                ->addColumn('action', function ($course) {
                    return view('pages.courses.action', [
                        'course' => $course,
                        'id' => $course->id,
                        'status' => $course->status,
                        'is_enabled' => $course->status == 1, 
                    ])->render();
                })
                ->editColumn('created_at', function ($course) {
                    return $course->created_at ? $course->created_at->format('d-m-Y') : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $html = $builder->columns([
            ['data' => 'title', 'name' => 'title', 'title' => 'Course Title'],
            ['data' => 'watch_time', 'name' => 'watch_time', 'title' => 'Watch Time (mins)'],
            ['data' => 'view_limit', 'name' => 'view_limit', 'title' => 'View Limit'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false, 'width' => '15%'],
        ]);

        return view('courses.index', compact('html'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'training_link' => 'nullable|url',
            'access_password' => 'nullable|string|max:255',
        ]);

        AdminCourse::create([
            'title' => $request->title,
            'description' => $request->description,
            'training_link' => $request->training_link,
            'access_password' => $request->access_password,
            // 'created_by' => auth()->id(),
        ]);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = ScormPackage::findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            // 'description' => 'required|string',
            'watch_time' => 'nullable|integer',
            'view_limit' => 'nullable|integer',
        ]);

        $course = ScormPackage::find($id);

        if (!$course) {
            return redirect()->route('courses.index')->with('error', 'Course not found.');
        }

        $course->title = $request->title;
        // $course->description = $request->description;
        $course->watch_time = $request->watch_time;
        $course->view_limit = $request->view_limit;
        $course->save();

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = ScormPackage::findOrFail($id);
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }

    public function coursetoggleStatus(Request $request, $id)
    {
        // dd("hello");
        try {
            $course = ScormPackage::findOrFail($id);

            if ($request->has('status')) {
                $course->status = (int) $request->input('status');
            } else {
                $course->status = $course->status == 1 ? 0 : 1;
            }

            $course->save();

            return response()->json([
                'success' => true,
                'status'  => $course->status,
                'message' => $course->status ? 'Course enabled successfully' : 'Course disabled successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('toggleStatus error: ' . $e->getMessage(), [
                'id' => $id,
                'user_id' => auth()->id() ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error while updating course status'
            ], 500);
        }
    }
}
