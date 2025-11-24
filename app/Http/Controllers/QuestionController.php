<?php

namespace App\Http\Controllers;

use App\Goal;
use Carbon\Carbon;
use App\Subchapter;
use App\Models\User;
use App\AskAQuestion;
use App\Models\Video;
use App\DifficultLevel;
use App\Models\Package;
use App\Models\Subject;
use App\Models\Professor;
use App\Models\PackageVideo;
use Illuminate\Http\Request;
use App\Models\SubjectPackage;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Facades\DataTables;

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
