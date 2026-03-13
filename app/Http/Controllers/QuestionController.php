<?php

namespace App\Http\Controllers;

use App\admin_test;
use App\AskAQuestion;
use App\Course;
use App\DifficultLevel;
use App\Goal;
use App\Models\Package;
use App\Models\PackageVideo;
use App\Models\Professor;
use App\Models\Subject;
use App\Models\SubjectPackage;
use App\Models\User;
use App\Models\Video;
use App\Question;
use App\ScormPackage;
use App\Subchapter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use test;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class QuestionController extends Controller
{
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $query = AskAQuestion::query();

            return DataTables::of($query)
                ->filter(function (\Illuminate\Database\Eloquent\Builder $query) {
                   
                    if (request()->filled('filter.search')) {
                        $query->where('question', request()->input('filter.search'));
                        $query->orwhereHas('package', function ($query) {
                            $query->where('name','LIKE', '%'.request('filter.search').'%');
                        });
                    }
                    if (request()->filled('filter.from_date') && request()->filled('filter.to_date')) {
                        $query->whereBetween('created_at', [date("Y-m-d H:i:s",strtotime(request()->input('filter.from_date').'00:00:00')), date("Y-m-d H:i:s",strtotime(request()->input('filter.to_date').'23:59:59'))]);
                    }
                    if(request()->input('filter.q_type')==1){
                        if (request()->filled('filter.professor')) {
                            $query->whereHas('answer', function ($query) {
                                $query->whereHas('user', function ($query) {
                                    $query->where('user_id', request()->input('filter.professor'));
                                });
                            });
                        }
                        $query->whereHas('answer');
                    }
                    else if(request()->input('filter.q_type')==2){
                        if (request()->filled('filter.professor')) {                            
                             $query->whereHas('video', function ($query) {
                                $professorID=request()->input('filter.professor');
                            $query->whereHas('professor', function ($query) use ($professorID) {
                                $query->where('user_id', $professorID);
                            });
                        });
                    }
                    $query->whereDoesntHave('answer');
                    }

                    if (request()->filled('filter.package')) {
                        $query->whereHas('package', function ($query) {
                            $query->where('package_id', request()->input('filter.package'));
                        });
                    }

                    if (! request()->input('filter')) {
                        $query->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                    }

                    $query->latest();
                })
                ->addColumn('package', function ($query) {
                    return optional($query->package)->name ?? '-';
                })
                ->addColumn('question', function ($query) {
                    return strlen($query->question) > 50 ? substr($query->question, 0, 50) . '...' : $query->question;
                })
                ->addColumn('answer', function ($query) {
                    if ($query->answer) {
                        return strlen($query->answer->answer) > 50 ? substr($query->answer->answer, 0, 50) . '...' : $query->answer->answer;
                    }
                })
                ->addColumn('asked_by', function ($query) {
                    return optional($query->user)->name ?? '-';
                })
                ->addColumn('answered_by', function ($query) {
                    return optional(optional($query->answer)->user)->name ?? '-';
                })
                ->addColumn('status', function ($query) {
                    if (! $query->answer) {
                        return '<span class="badge badge-warning">Submitted</span>';
                    }

                    return '<span class="badge badge-success">Responded</span>';
                })
                ->addColumn('asked_at', function ($query) {
                    if (! $query->answer && Carbon::parse($query->created_at)->addHours(48)->lessThan(Carbon::now())) {
                        return Carbon::parse($query->created_at)->toDayDateTimeString() . '<br>' . '<span class="badge badge-danger">48 Hrs crossed</span>';
                    }

                    if (! $query->answer && Carbon::parse($query->created_at)->addHours(24)->lessThan(Carbon::now())) {
                        return Carbon::parse($query->created_at)->toDayDateTimeString() . '<br>' . '<span class="badge badge-warning">24 Hrs crossed</span>';
                    }

                    return Carbon::parse($query->created_at)->toDayDateTimeString();
                })
                ->addColumn('action', function ($query) {
                    return '<a href="' . route('questions.show', $query->id) . '"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['status', 'asked_at', 'action'])
                ->make(true);
        }

        $table = $builder->columns([
            ['name' => 'ID', 'data' => 'id', 'title' => 'ID'],
            ['name' => 'package', 'data' => 'package', 'title' => 'Package', 'width' => '20%'],
            ['name' => 'question', 'data' => 'question', 'title' => 'Question'],
            ['name' => 'answer', 'data' => 'answer', 'title' => 'Answer'],
            ['name' => 'asked_by', 'data' => 'asked_by', 'title' => 'Asked By'],
            ['name' => 'answered_by', 'data' => 'answered_by', 'title' => 'Answered By'],
            ['name' => 'status', 'data' => 'status', 'title' => 'Status'],
            ['name' => 'asked_at', 'data' => 'asked_at', 'title' => 'Asked At'],
            ['name' => 'action', 'data' => 'action', 'title' => '']
        ])->parameters([
            'searching' => false,
            'ordering' => false,
            'lengthChange' => false
        ]);

        return view('pages.questions.index', compact('table'));
    }

    public function show($id)
{
    $question = AskAQuestion::query()->with('answer')->findOrFail($id);
    $user = User::query()->with('student.course', 'student.level')->find($question->user_id);
    $package = Package::query()->with('course', 'level', 'subject', 'chapter')->find($question->package_id);

    return view('pages.questions.show', compact('user', 'package', 'question'));
}


    public function professors(Builder $builder)
    {
        $answeredQuestions = app(Builder::class)->columns([
            ['name' => 'name', 'data' => 'name', 'title' => 'Name'],
            ['name' => 'answered_questions', 'data' => 'answered_questions', 'title' => 'Question count'],
            ['name' => 'total_questions', 'data' => 'total_questions', 'title' => 'Total Questions'],
            ['name' => 'action', 'data' => 'action', 'title' => '']
        ])->parameters([
            'lengthChange' => false,
            'searching' => false,
            'ordering' => false
        ])->ajax(url('fetch-answered-questions'))->setTableId('tbl-answered-questions');


        $pendingQuestions = app(Builder::class)->columns([
            ['name' => 'name', 'data' => 'name', 'title' => 'Name'],
            ['name' => 'pending_questions', 'data' => 'pending_questions', 'title' => 'Question count'],
            ['name' => 'total_questions', 'data' => 'total_questions', 'title' => 'Total Questions'],
            ['name' => 'action', 'data' => 'action', 'title' => '']
        ])->parameters([
            'lengthChange' => false,
            'searching' => false,
            'ordering' => false
        ])->ajax(url('fetch-pending-questions'))->setTableId('tbl-pending-questions');

        return view('pages.questions.professors.index', compact('answeredQuestions','pendingQuestions'));
       
    }

    public function fetchAnsweredQuestions(Builder $builder){

        if (request()->ajax()) {
        $query = Professor::query();

        return DataTables::of($query)
                ->filter(function ($query) {
                    if (request()->filled('filter.search')) {
                        return $query->where('name', 'like', '%' . request()->input('filter.search') . '%');
                    }

                    $query->orderBy('name', 'asc');
                })
               
                
                ->addColumn('answered_questions', function ($query) {
                    $professorID = $query->id;

                    return AskAQuestion::query()
                        ->whereHas('video', function ($query) use ($professorID) {
                            $query->whereHas('professor', function ($query) use ($professorID) {
                                $query->where('id', $professorID);
                            });
                        })->whereHas('answer')
                        ->count();
                })
                ->addColumn('total_questions', function ($query) {
                    $professorID = $query->id;

                    return AskAQuestion::query()
                        ->whereHas('video', function ($query) use ($professorID) {
                            $query->whereHas('professor', function ($query) use ($professorID) {
                                $query->where('id', $professorID);
                            });
                        })
                        ->count();
                })
                ->addColumn('action', function ($query) {
                    return '<a href="' . url('questions') . '?professor_user_id=' . $query->user_id . '&&answered=1"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

    }

    public function fetchPendingQuestions(Builder $builder){
        if (request()->ajax()) {
            $query = Professor::query();
    
            return DataTables::of($query)
                    ->filter(function ($query) {
                        if (request()->filled('filter.search')) {
                            return $query->where('name', 'like', '%' . request()->input('filter.search') . '%');
                        }    
                        $query->orderBy('name', 'asc');
                    })
                   
                    
                    ->addColumn('pending_questions', function ($query) {
                        $professorID = $query->id;
    
                        return AskAQuestion::query()
                            ->whereHas('video', function ($query) use ($professorID) {
                                $query->whereHas('professor', function ($query) use ($professorID) {
                                    $query->where('id', $professorID);
                                });
                            })->whereDoesntHave('answer')
                            ->count();
                    })
                    ->addColumn('total_questions', function ($query) {
                        $professorID = $query->id;
    
                        return AskAQuestion::query()
                            ->whereHas('video', function ($query) use ($professorID) {
                                $query->whereHas('professor', function ($query) use ($professorID) {
                                    $query->where('id', $professorID);
                                });
                            })
                            ->count();
                    })
                    ->addColumn('action', function ($query) {
                        return '<a href="' . url('questions') . '?professor_user_id=' . $query->user_id . '&&answered=2"><i class="fas fa-eye"></i></a>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
    
    }

    public function getprofessorPackages(Request $request){
        $professor =Professor::where('user_id',$request->id)->first();
        $videoIDs = Video::where('professor_id',$professor->id )->get()->pluck('id');
        $chapterPackageIDs = PackageVideo::whereIn('video_id', $videoIDs)->get()->pluck('package_id')->unique();
        $subjectPackageIDs = SubjectPackage::whereIn('chapter_package_id', $chapterPackageIDs)->get()->pluck('package_id')->unique();

        $packageIDs = [];

        foreach($chapterPackageIDs as $chapterPackageID) {
            $packageIDs[] = $chapterPackageID;
        }

        foreach($subjectPackageIDs as $subjectPackageID) {
            $packageIDs[] = $subjectPackageID;
        }
        $response = Package::whereIn('id',$packageIDs)->get();
        return response()->json($response, 200);
    }

    public function adminTest(){

        // 
        $admin_test = admin_test::all();
        return view('admintest',compact('admin_test'));
    
    }

    public function adminTestCreate(){

        $course = ScormPackage::all();
        // dd($course);
        return view('admintestcreate',compact('course'));
    }

    public function adminTestSave(Request $request){
        
        // Validate the request
        $validated = $request->validate([
            'test_name' => 'required|string|max:255',
            'course_id' => 'required|integer',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'integer',
            'total_ques_count' => 'required|integer|min:1',
            'easy_count' => 'required|integer|min:0',
            'medium_count' => 'required|integer|min:0',
            'hard_count' => 'required|integer|min:0',
        ]);

        // Verify that the sum of difficulty counts equals total questions
        $difficultySum = $validated['easy_count'] + $validated['medium_count'] + $validated['hard_count'];
        if ($difficultySum != $validated['total_ques_count']) {
            return back()->withErrors(['total_ques_count' => 'Sum of easy, medium, and hard questions must equal total questions.'])->withInput();
        }

        try {
            // Create admin test record with comma-separated subject IDs
            $subjectIdsString = implode(',', $validated['subject_ids']);
            
            $adminTest = admin_test::create([
                'test_name' => $validated['test_name'],
                'course_id' => $validated['course_id'],
                'subject_id' => $subjectIdsString,
                'total_ques_count' => $validated['total_ques_count'],
                'easy_count' => $validated['easy_count'],
                'medium_count' => $validated['medium_count'],
                'hard_count' => $validated['hard_count'],
                'status' => 1,
            ]);

            return redirect()->route('admin-test')->with('success', 'Test created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error creating test: ' . $e->getMessage()])->withInput();
        }

    }

    public function toggleAdminTestStatus(Request $request, $id){
        try {
            $adminTest = admin_test::find($id);
            if(!$adminTest) {
                return response()->json(['success' => false, 'message' => 'Test not found'], 404);
            }

            $adminTest->update([
                'status' => $request->input('status')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $adminTest->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getQuestionsByCourseSubject($courseId, $subjectId = null)
    {
        $query = Question::where('course_id', $courseId)
            ->select('id', 'question', 'question_number')
            ->orderBy('id', 'asc');

        if (!is_null($subjectId) && $subjectId !== '' ) {
            $query->where('subject_id', $subjectId);
        }

        $questions = $query->get();

        return response()->json([
            'questions' => $questions
        ]);
    }
    
    // public function getSubchaptersByChapterId(Request $request)
    // {
    //     $chapter_id = $request->input('chapter_id');

    //     if (!$chapter_id) {
    //         return response()->json([
    //             'error' => true,
    //             'message' => 'Chapter ID is required',
    //             'data' => []
    //         ], 400);
    //     }

    //     $subchapters = Subchapter::where('chapter_id', $chapter_id)
    //         // ->where('status', 1)
    //         ->get();

    //     return response()->json([
    //         'error' => false,
    //         'data' => $subchapters
    //     ]);
    // }
}
