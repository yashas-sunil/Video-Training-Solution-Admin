<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Video;
use App\Models\Chapter;
use App\Models\Subject;
use App\Models\LevelType;
use App\Models\Professor;
use App\Models\PackageType;
use App\ScormPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Builder $builder
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $query = Subject::query()
                ->with('level')
                ->with('package_type')
                ->with('course')
                ->whereHas('course')
                ->whereHas('level');

            if (request()->filled('filter.search')) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . request()->input('filter.search') . '%')
                        ->orWhere(function ($query) {
                            $query->where(function ($query) {
                                $query->whereHas('course', function ($query) {
                                    $query->where('name', 'like', '%' . request()->input('filter.search') . '%');
                                })->orWhereHas('level', function ($query) {
                                    $query->where('name', 'like', '%' . request()->input('filter.search') . '%');
                                })->orWhereHas('package_type', function ($query) {
                                    $query->where('name', 'like', '%' . request()->input('filter.search') . '%');
                                });
                            });
                        });
                });
            }

            if (request()->filled('filter.course')) {
                $query->where(function ($query) {
                    $query->where('course_id', request()->input('filter.course'));
                });
            }
            if (request()->filled('filter.level')) {
                $query->where(function ($query) {
                    $query->where('level_id', request()->input('filter.level'));
                });
            }
            if (request()->filled('filter.package_type')) {
                $query->where('package_type_id', request()->input('filter.package_type'));
            }

            return DataTables::of($query)

                ->addColumn('action', 'pages.subjects.action')
                ->editColumn('package_type.name', function ($query) {
                    if ($query->package_type) {
                        return $query->package_type->name;
                    }
                    return '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $html = $builder->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Subject'],
            ['data' => 'package_type.name', 'name' => 'package_type.name', 'title' => 'Type', 'orderable' => false],
            ['data' => 'level.name', 'name' => 'level.name', 'title' => 'Level', 'orderable' => false],
            ['data' => 'course.name', 'name' => 'course.name', 'title' => 'Course', 'orderable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => '', 'searchable' => false, 'orderable' => false]
        ]);

        return view('pages.subjects.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = PackageType::where('is_enabled', true)->get();
        return view('pages.subjects.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'course_id' => 'required',
            'level_id' => 'required',
        ])->validate();

        $subject = new Subject();
        $subject->name = $request->input('name');
        $subject->course_id = $request->input('course_id');
        $subject->package_type_id = $request->input('package_type');
        $subject->level_id = $request->input('level_id');
        $subject->save();

        return redirect(route('subjects.index'))->with('success', 'Subject successfully created');
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
        $subject = Subject::with('course', 'level', 'package_type')->findOrFail($id);
        $types = LevelType::with(['packagetype' => function ($types) {
            $types->where('is_enabled', TRUE);
        }])
            ->where('level_id', $subject->level_id)
            ->get();
        return view('pages.subjects.edit', compact('subject', 'types'));
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'course_id' => 'required',
            'level_id' => 'required',
        ])->validate();

        $subject = Subject::findOrFail($id);

        $subject->course_id = $request->course_id;
        $subject->level_id = $request->level_id;
        $subject->package_type_id = $request->package_type;
        $subject->name = $request->name;

        $subject->save();

        return redirect(route('subjects.index'))->with('success', 'Subject successfully updated');;
    }

    //    /**
    //     * Remove the specified resource from storage.
    //     *
    //     * @param  int  $id
    //     * @return \Illuminate\Http\Response
    //     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        // $subject->delete();

        // return response()->json(true, 200);

        /***************TE Modified***********/
        if ($subject->is_enabled == true) {
            $subject->is_enabled = false;
        } else {
            $subject->is_enabled = true;
        }
        $subject->save();
        return response()->json(true, 200);

        /***************TE ends*************/
    }
    //
    //    public function level_from_course($course_id){
    //
    //        $level_list = Level::where('course_id', $course_id)->get();
    //
    //        $str ="";
    //
    //        foreach ($level_list as $level)
    //        {
    //            $str .= '<option value="'.$level->id.'">';
    ////            if( $level_id== $level->id)
    ////                $str .= 'selected="selected">';
    ////            else
    ////                $str .= '>';
    //            $str .= $level->name.'</option>';
    //        }
    //        echo $str;
    //    }

    public function LevelSubjects(Request $request)
    {

        $levelId = $request->id;
        $subjects = Subject::where('level_id', $levelId)
            ->orderBy('name', 'asc')
            ->get();

        return json_encode($subjects);
    }

    public function SubjectProfessors(Request $request)
    {

        $professorIds = Video::where('subject_id', $request->id)->pluck('professor_id');
        $professorIds = collect($professorIds);
        $professorIds = $professorIds->unique();
        $professors = Professor::whereIn('id', $professorIds)->orderBy('name')->get();
        return json_encode($professors);
    }

    public function getChapterBySubjectId(Request $request)
    {
        /*"We retrieve all topics of a particular chapter. 
        In $idStatusOne, we have those topics with a status of 1. 
        In $idNoInc, we have those topics with a status of 0, 
        but only if there is at least one topic with a status of 1. 
        Therefore, if there is one topic with a status of 1 and another with a status of 0, we should not allow the creation of a subchapter, as one active topic is present."
        */
        if (isset($request->noSubjTopic)) {
            $idStatusOne = [];
            $idNoInc = [];
            $id = $request->input('subjects_id');
            $idDetails = DB::table('topics')
                ->where('topics.subject_id', $id)
                ->whereNull('topics.subchapter_id')
                ->select('topics.*')
                ->get()
                ->toArray();
            foreach ($idDetails as $idDetail) {
                if ($idDetail->status == 1) {
                    $idStatusOne[$idDetail->chapters_id][] = $idDetail->id;
                }
            }

            foreach ($idDetails as $idDetail) {
                foreach ($idStatusOne as $key => $value) {
                    if ($idDetail->chapters_id == $key) {
                        $idNoInc[] = $idDetail->id;
                    }
                }
            }

            $chapters = Chapter::select('chapters.*')
                ->leftJoin('topics', 'chapters.id', '=', 'topics.chapters_id')
                ->where('chapters.status', 1)
                ->where('chapters.subjects_id', $id)
                ->where(function ($query) use ($id) {
                    $query->where(function ($q) use ($id) {
                        $q->whereNotNull('topics.subchapter_id');
                    })
                        ->orWhere(function ($q) use ($id) {
                            $q->where('topics.status', 0);
                        })
                        ->orWhereNull('topics.chapters_id');
                })
                ->where(function ($query) use ($idNoInc) {
                    $query->whereNotIn('topics.id', $idNoInc)
                        ->orWhereNull('topics.id');
                })
                ->distinct()
                ->get();

            return response()->json($chapters);
        }
        $id = $request->input('subjects_id');
        $chapters = Chapter::where('subjects_id', $id)->where('status', Subject::ACTIVE)->get();
        return response()->json($chapters);
    }

    public function createsubject()
    {
        $courses = ScormPackage::all();
        return view('subjects.create', compact('courses'));
    }

    // Store Multiple Subjects
   public function storesubject(Request $request)
{
    $request->validate([
        'course_id' => 'required',
        'subjects' => 'required|array',
        'subjects.*' => 'required|string|max:255'
    ], [
        'course_id.required' => 'Please select a course.',
        'subjects.required' => 'Please add at least one subject.',
        'subjects.*.required' => 'Subject name is required.',
    ]);

    foreach ($request->subjects as $subjectName) {

        $exists = Subject::where('course_id', $request->course_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($subjectName)])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'duplicate' => "Subject '{$subjectName}' is already assigned to this course."
                ]);
        }

        Subject::create([
            'course_id' => $request->course_id,
            'name' => $subjectName,
            'status' => 1
        ]);
    }

    return redirect()->route('subjects.index')
        ->with('success', 'Subjects Added Successfully');
}
    public function getChaptersByCourse($courseId)
    {
        $subjects = Subject::where('course_id', $courseId)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'subjects' => $subjects
        ]);
    }

    public function subjectindex(Builder $builder)
    {
        if (request()->ajax()) {

            $query = Subject::join('scorm_packages', 'subjects.course_id', '=', 'scorm_packages.id')
                ->select(
                    'subjects.*',
                    'scorm_packages.title as course_title'
                );

            return DataTables::of($query)

                ->addColumn('course_title', fn($subject) => $subject->course_title)

                ->addColumn('subject_name', fn($subject) => $subject->name)

                ->addColumn('action', function ($subject) {

                    $editUrl = route('subjects.edit', $subject->id);

                    return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning">
                        Edit
                    </a>
                ';
                })

                ->rawColumns(['action'])

                ->make(true);
        }

        $html = $builder->columns([

            [
                'data'  => 'course_title',
                'name'  => 'scorm_packages.title',
                'title' => 'Course Title',
                'orderable' => true,
                'searchable' => true
            ],

            [
                'data'  => 'subject_name',
                'name'  => 'subjects.name',
                'title' => 'Subject Name',
                'orderable' => true,
                'searchable' => true
            ],

            [
                'data'  => 'action',
                'name'  => 'action',
                'title' => 'Action',
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
                'width' => '100px',
            ],
        ]);

        return view('subjects.index', compact('html'));
    }

    public function subjectedit($id)
    {
        $subject = Subject::findOrFail($id);

        $courses = DB::table('scorm_packages')
            ->select('id', 'title')
            ->get();

        return view('subjects.edit', compact('subject', 'courses'));
    }

    public function subjectupdate(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required',
            'name' => 'required|string|max:255'
        ]);

        $subject = Subject::findOrFail($id);

        $exists = Subject::where('course_id', $request->course_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' => 'This subject is already assigned to this course.'
                ]);
        }

        $subject->update([
            'course_id' => $request->course_id,
            'name' => $request->name
        ]);

        return redirect()->route('subjects.index')
            ->with('success', 'Subject Updated Successfully');
    }
    public function getSubjects($course_id)
    {
        $subjects = Subject::where('course_id', $course_id)->get();
        return response()->json($subjects);
    }
}
