<?php

namespace App\Http\Controllers;

use App\Batch;
use App\Models\User;
use App\ScormPackage;
use Illuminate\Http\Request;

class BatchController extends Controller
{
       public function index()
{
    $batches = Batch::with(['courses', 'students'])
        ->latest()
        ->get();

    return view('batches.index', compact('batches'));
}


    public function create()
    {
    $courses = ScormPackage::select('id', 'title')->orderBy('title')->get();
       // dd($courses);
        return view('batches.create', compact('courses'));
    }

   public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'course_ids' => 'required|array',
        'start_date' => 'required|date',
        'expiry_date' => 'required|date|after:start_date',
    ]);

    $batch = Batch::create([
        'batch_name' => $request->name,
        'start_date' => $request->start_date,
        'expire_date' => $request->expiry_date,
    ]);

    // attach multiple courses
    $batch->courses()->attach($request->course_ids);

    return redirect()->route('batches.index')
        ->with('success', 'Batch created successfully');
}



   public function assignStudents($id)
{
    $batch = Batch::findOrFail($id);

    $students = User::where('role', '2')->get();

    $assignedStudents = $batch->students()->pluck('users.id')->toArray();

    return view('batches.assign', compact('batch', 'students', 'assignedStudents'));
}


    public function storeStudents(Request $request, $id)
    {
        $batch = Batch::findOrFail($id);
        $batch->students()->sync($request->students);

        return redirect()->route('batches.index')
            ->with('success', 'Students Assigned Successfully');
    }

      public function downloadSampleExcel()
    {
      //  dd("sample excel");
        $path = public_path('Documents/ExcelSample/new_excel.zip');

        if (!file_exists($path)) {
            abort(404, 'File not found at: ' . $path);
        }

        return response()->download($path);
    }

}
