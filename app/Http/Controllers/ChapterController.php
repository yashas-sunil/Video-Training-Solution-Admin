<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Package;
use App\Models\PackageType;
use App\Models\LevelType;
use DemeterChain\C;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\Validator;


class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder)
    {
        $subjects = Subject::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        if (request()->ajax()) {
            $query = Chapter::query()
                ->with('level')
                ->with('course')
                ->with('package_type')
                ->with('subject')
                ->whereHas('course');
            if (request()->filled('filter.search')) {
                $query->where(function ($query) {
                    $query->where('name','like','%'. request()->input('filter.search') .'%')
                        ->orWhere(function ($query) {
                            $query->where(function ($query) {
                                $query->whereHas('course', function ($query) {
                                    $query->where('name', 'like', '%' . request()->input('filter.search') . '%');
                                })->orWhereHas('level', function ($query) {
                                    $query->where('name', 'like', '%' . request()->input('filter.search') . '%');
                                })->orWhereHas('subject', function ($query) {
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
            if (request()->filled('filter.subject')) {
                $query->where(function ($query) {
                    $query->where('subject_id', request()->input('filter.subject'));
                });
            }

            return DataTables::of($query)
                ->addColumn('action', 'pages.chapters.action')
                ->editColumn('package_type.name', function($query) {
                    if ($query->package_type) {
                        return $query->package_type->name;
                    }
                    return '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $html = $builder->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Chapter'],
            ['data' => 'subject.name', 'name' => 'name', 'title' => 'Subject'],
            ['data' => 'package_type.name', 'name' => 'package_type.name', 'title' => 'Type', 'orderable' => false],
            ['data' => 'level.name', 'name' => 'name', 'title' => 'Level',  'searchable' => false, 'orderable' => false],
            ['data' => 'course.name', 'name' => 'name', 'title' => 'Course',  'searchable' => false, 'orderable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => '', 'searchable' => false, 'orderable' => false, 'width' => '10%']
        ]);

        return view('pages.chapters.index', compact('html','courses','subjects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = PackageType::where('is_enabled',true)->get();
        return view('pages.chapters.create',compact('types'));
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
            'subject_id' => 'required',
        ])->validate();

        DB::beginTransaction();
        $chapter = new Chapter();
        $chapter->name = $request->input('name');
        $chapter->course_id = $request->input('course_id');
        $chapter->level_id = $request->input('level_id');
        $chapter->package_type_id =$request->input('package_type');
        $chapter->subject_id = $request->input('subject_id');
        $chapter->save();
        $files = $request->file('study_materials');
        if($request->hasFile('study_materials'))
        {
            foreach ($files as $file) {
                $filename = time()  .$file->getClientOriginalName();
                $file->storeAs('public/study_materials/', $filename);
                $study_material = new StudyMaterial();
                $study_material->chapter_id = $chapter->id;
                $study_material->file_name = $filename;
                $study_material->save();
            }
        }
        DB::commit();
        return redirect(route('chapters.index'))->with('success', 'Chapter successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function show(Chapter $chapter)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function edit(Builder $builder,$id)
    {
        if (request()->ajax()) {
            $query = StudyMaterial::where('chapter_id','=',$id)->get();

            return DataTables::of($query)
                ->editColumn('file', function($query) {
                    if($query->file){
                        return '<span class="text-center">
                            <a target="_blank" href="' .$query->file. '"><i style="color: #bb0118!important;" class="far fa-file-pdf">
                            <span class="p-3" style="color: black">'.$query->file_name.'<span>
                            </span></i></a>
                            </span>';
                    }
                    else{
                        return null;
                    }
                })
                ->addColumn('action', 'pages.chapters.study_materials.action')
                ->rawColumns(['action','file'])
                ->make(true);
        }

        $html = $builder->columns([
            ['data' => 'file', 'name' => 'file', 'title' => 'File Name'],
            ['data' => 'action', 'name' => 'action', 'title' => '', 'searchable' => false, 'orderable' => false, 'width' => '20%']
        ]);
        $study_materials = StudyMaterial::where('chapter_id','=',$id)->count();
        $chapter = Chapter::with('course', 'level', 'subject','package_type')->findOrFail($id);
        $types = LevelType::with(['packagetype'=> function($types){
            $types->where('is_enabled', TRUE);
        }])
        ->where('level_id',$chapter->level_id)
        ->get();

        return view('pages.chapters.edit', compact('chapter','html','study_materials','types'));
    }


    public function updateStudyMaterial(Request  $request){

        $validator = Validator::make($request->all(), [
            'study_material' => 'mimes:pdf|max:10000'
        ])->validate();

        $update_study_material = StudyMaterial::find($request->id);
        if($request->hasFile('study_material')) {
            $file = $request->file('study_material');
            $filename = time()  .$file->getClientOriginalName();
            $file->storeAs('public/study_materials/', $filename);
            $update_study_material->file_name = $filename;
        }
        $update_study_material->update();
        return redirect()->back()->with('success', 'File updated successfully!');
    }

    public function deleteStudyMaterials(Request  $request){

        $study_material = StudyMaterial::find($request->id);
        $study_material->delete();

        return response()->json(true, 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'course_id' => 'required',
            'level_id' => 'required',
            'subject_id' => 'required',
//            'study_materials[]' => 'mimes:pdf|max:10000'
        ])->validate();

        DB::beginTransaction();

        $chapter = Chapter::findOrFail($id);
        $chapter->course_id = $request->course_id;
        $chapter->level_id = $request->level_id;
        $chapter->package_type_id =$request->package_type;
        $chapter->subject_id = $request->subject_id;
        $chapter->name = $request->name;
        $chapter->save();

//        $files = $request->file('study_materials');
//        if($request->hasFile('study_materials'))
//        {
//            foreach ($files as $file) {
//                $filename = time()  .$file->getClientOriginalName();
//                $file->storeAs('public/study_materials/', $filename);
//                $study_material = new StudyMaterial();
//                $study_material->chapter_id = $id;
//                $study_material->file_name = $filename;
//                $study_material->save();
//            }
//        }
        DB::commit();

        return redirect(route('chapters.index'))->with('success', 'Chapter successfully updated');;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $chapter = Chapter::findOrFail($id);

         // $chapter->delete();

        // return response()->json(true, 200);
         /***************TE Modified***********/
         if($chapter->is_enabled==true){

            $chapter->is_enabled= false;
           
        }
    else{
        $chapter->is_enabled=true;
       
    }       
    $chapter->save();
    return response()->json(true, 200);

    /***************TE ends*************/
    }

    public function SubjectChapters(Request  $request){
        $subjectId = $request->id;
        $chapters = Chapter::where('subject_id', $subjectId)
            ->orderBy('name','asc')
            ->get();

        return json_encode($chapters);
    }

    public function getSubjectsByCourse($courseId)
    {
        $subjects = Subject::where('course_id', $courseId)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'subjects' => $subjects
        ]);
    }
}
