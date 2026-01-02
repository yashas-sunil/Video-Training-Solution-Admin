<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\CourseView;
use App\CourseProgress;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;
use App\Services\ScormCloudService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\ScormPackage as AppScormPackage;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use App\CourseProgress as AppCourseProgress;

class ScormController extends Controller
{
    public function showForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        Log::info("SCORM Upload Start (SIMPLE FORM)");

        $request->validate([
            'title' => 'required|string|max:255',
            'watch_time' => 'required|numeric|min:1',
            'view_limit_option' => 'required|string',
            'view_limit' => 'nullable|numeric|min:1',
        ]);

        Log::info("Validation Passed");

        $viewLimit = $request->view_limit_option === 'custom'
            ? $request->view_limit
            : intval($request->view_limit_option);

        AppScormPackage::create([
            'title' => $request->title,
            // 'folder_name' => null,
            // 'launch_file' => null,   
            'watch_time' => $request->watch_time,
            'view_limit' => $viewLimit,
        ]);

        Log::info("SCORM saved successfully");

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function uploadChapterScorm(Request $request)
{
    try {

        Log::info("Chapter SCORM Upload Start");

        $request->validate([
            'course_id'    => 'required|exists:scorm_packages,id',
            'chapter_name' => 'required|string|max:255',
            'zip_file'     => 'required|file|mimes:zip|max:102400',
        ]);

        if (!$request->hasFile('zip_file')) {
            Log::error('Zip file missing in request');
            return back()->with('error', 'Zip file missing');
        }

        $zip = $request->file('zip_file');

        Log::info('Zip received', [
            'original_name' => $zip->getClientOriginalName(),
            'size'          => $zip->getSize(),
            'mime'          => $zip->getMimeType(),
            'tmp_path'      => $zip->getPathname(),
        ]);

        $folderName  = 'scorm_' . time();
        $extractPath = public_path('scorm_packages/' . $folderName);

        if (!File::exists($extractPath)) {
            File::makeDirectory($extractPath, 0755, true);
        }

        if (!File::isWritable($extractPath)) {
            Log::error('Extract path not writable', ['path' => $extractPath]);
            return back()->with('error', 'Folder permission issue');
        }

        $zipPath = $extractPath . '/' . $zip->getClientOriginalName();

        $zip->move($extractPath, $zip->getClientOriginalName());

        if (!file_exists($zipPath)) {
            Log::error('Zip move failed', ['zipPath' => $zipPath]);
            return back()->with('error', 'Zip move failed');
        }

        Log::info('Zip moved successfully', ['zipPath' => $zipPath]);

        $zipArchive = new \ZipArchive;
        $openStatus = $zipArchive->open($zipPath);

        if ($openStatus !== true) {
            Log::error('Zip open failed', ['status' => $openStatus]);
            return back()->with('error', 'Zip open failed: ' . $openStatus);
        }

        $zipArchive->extractTo($extractPath);
        $zipArchive->close();
        unlink($zipPath);

        Log::info('Zip extracted successfully');

        $manifestPath = $this->findManifest($extractPath);

        if (!$manifestPath || !file_exists($manifestPath)) {
            Log::error('Manifest not found', ['path' => $extractPath]);
            return back()->with('error', 'SCORM manifest not found');
        }

        $xml = simplexml_load_file($manifestPath);
        if (!$xml) {
            Log::error('Manifest XML load failed');
            return back()->with('error', 'Invalid manifest XML');
        }

        $xml->registerXPathNamespace('ns', 'http://www.imsproject.org/xsd/imscp_rootv1p1p2');
        $resource = $xml->xpath('//ns:resource')[0] ?? null;

        if (!$resource) {
            Log::error('SCORM resource node missing');
            return back()->with('error', 'Invalid SCORM package');
        }

        $base = (string) $resource['base'];
        $href = (string) $resource['href'];

        $launchFile = $base ? ($base . '/' . $href) : $href;
        $launchFullPath = dirname($manifestPath) . '/' . $launchFile;

        if (!file_exists($launchFullPath)) {
            Log::error('Launch file missing', ['file' => $launchFullPath]);
            return back()->with('error', 'Launch file not found');
        }

        Chapter::create([
            'course_id'   => $request->course_id,
            'name'        => $request->chapter_name,
            'folder_name' => $folderName,
            'launch_file' => str_replace($extractPath . '/', '', $launchFullPath),
        ]);

        Log::info('Chapter SCORM uploaded successfully');

        return redirect()
            ->route('chapters')
            ->with('success', 'Chapter SCORM uploaded successfully.');

    } catch (\Throwable $e) {
        Log::error('Chapter SCORM Upload Fatal Error', [
            'message' => $e->getMessage(),
            'line'    => $e->getLine(),
            'file'    => $e->getFile(),
        ]);

        return back()->with('error', 'Server error during upload');
    }
}



    /**
     * 🔍 Recursively find imsmanifest.xml in extracted folders
     */
    private function findManifest($dir)
    {
        foreach (File::allFiles($dir) as $file) {
            if ($file->getFilename() === 'imsmanifest.xml') {
                return $file->getPathname();
            }
        }
        return null;
    }
    public function view($id)
    {
        $package = AppScormPackage::findOrFail($id);
        $userId = auth()->id();

        // Course total watch time (minutes → seconds)
        $watchTime = $package->watch_time * 60;

        // Get existing view record for this user & course
        $courseView = CourseView::where('user_id', $userId)
            ->where('course_id', $id)
            ->first();

        if (!$courseView) {
            $courseView = CourseView::create([
                'user_id' => $userId,
                'course_id' => $id,
                'view_limit' => 1,
            ]);
        }

        $totalSessionTime = AppCourseProgress::where('user_id', $userId)
            ->where('course_id', $id)
            ->sum('session_time');

        // Formula: totalSessionTime - ((attempt_no - 1) * watchTime)
        $currentAttemptTime = max(0, $totalSessionTime - (($courseView->view_limit - 1) * $watchTime));

        if ($currentAttemptTime >= $watchTime) {
            $courseView->increment('view_limit');
            $courseView->update(['last_reset_time' => now()]);
        }

        $launchUrl = asset('scorm_packages/' . $package->folder_name . '/' . $package->launch_file);

        $progress = AppCourseProgress::where('user_id', $userId)
            ->where('course_id', $id)
            ->first();

        return view('view', [
            'launchUrl'    => $launchUrl,
            'title'        => $package->title,
            'courseId'     => $id,
            'resumeTime'   => $progress->resume_from_time ?? 0,
            'lastLocation' => $progress->cmi_core_lesson_location ?? '',
            'lessonStatus' => $progress->cmi_core_lesson_status ?? '',
            'score'        => $progress->cmi_core_score_raw ?? '',
            'suspendData'  => $progress->progress_data['suspend_data'] ?? '',
        ]);
    }
    public function saveProgress(Request $request)
    {
        $user = auth()->user();

        CourseProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $request->course_id
            ],
            [
                $request->key => $request->value, // will save either lesson_location or lesson_status
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    public function showCourses(ScormCloudService $scorm)
    {
        // dd(config('scorm.app_id'), config('scorm.secret'));

        $courses = $scorm->getCourses();
        return response()->json($courses);
    }
    public function Chapters()
    {
        $courses = AppScormPackage::select('id', 'title')->get();

        return view('courses.chapter', compact('courses'));
    }

    public function chapterindex(Builder $builder)
    {
        if (request()->ajax()) {

            $query = Chapter::join('scorm_packages', 'chapters.course_id', '=', 'scorm_packages.id')
                ->select('chapters.*', 'scorm_packages.title as course_title');

            return DataTables::of($query)
                ->addColumn('course_title', function ($chapter) {
                    return $chapter->course_title;
                })
                ->addColumn('chapter_name', function ($chapter) {
                    return $chapter->name;
                })
                ->rawColumns(['course_title', 'chapter_name'])
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
                'data'  => 'chapter_name',
                'name'  => 'chapters.name',
                'title' => 'Chapter Name',
                'orderable' => true,
                'searchable' => true
            ],
        ]);

        return view('courses.chapterlist', compact('html'));
    }

    public function chapterList($courseId)
    {
        dd($courseId);
        $chapters = Chapter::where('course_id', $courseId)->get();

        return view('chapters.list', [
            'chapters' => $chapters,
            'courseId' => $courseId
        ]);
    }

    public function viewChapter($id)
    {
        $chapter = Chapter::findOrFail($id);
        $userId = auth()->id();

        // watch time agar chapter level pe hai
        $watchTime = ($chapter->watch_time ?? 0) * 60;

        $courseView = CourseView::where('user_id', $userId)
            ->where('course_id', $chapter->course_id)
            ->first();

        if (!$courseView) {
            $courseView = CourseView::create([
                'user_id' => $userId,
                'course_id' => $chapter->course_id,
                'view_limit' => 1,
            ]);
        }

        $totalSessionTime = AppCourseProgress::where('user_id', $userId)
            ->where('course_id', $chapter->course_id)
            ->sum('session_time');

        $currentAttemptTime = max(
            0,
            $totalSessionTime - (($courseView->view_limit - 1) * $watchTime)
        );

        if ($watchTime > 0 && $currentAttemptTime >= $watchTime) {
            $courseView->increment('view_limit');
            $courseView->update(['last_reset_time' => now()]);
        }

        $launchUrl = asset(
            'scorm_packages/' . $chapter->folder_name . '/' . $chapter->launch_file
        );

        $progress = AppCourseProgress::where('user_id', $userId)
            ->where('course_id', $chapter->course_id)
            ->first();

        return view('view', [
            'launchUrl'    => $launchUrl,
            'title'        => $chapter->name,
            'courseId'     => $chapter->course_id,
            'resumeTime'   => $progress->resume_from_time ?? 0,
            'lastLocation' => $progress->cmi_core_lesson_location ?? '',
            'lessonStatus' => $progress->cmi_core_lesson_status ?? '',
            'score'        => $progress->cmi_core_score_raw ?? '',
            'suspendData'  => $progress->progress_data['suspend_data'] ?? '',
        ]);
    }
    public function getChapters($courseId)
    {
        // dd($courseId);
        $chapters = Chapter::where('course_id', $courseId)
            ->orderBy('id', 'asc')
            ->get(['id', 'name']);

        return response()->json($chapters);
    }
}
