<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\CourseView;
use App\Models\User;
use App\Models\Lesson;
use App\CourseProgress;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;
use App\Services\ScormCloudService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ChapterManualContent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\ScormPackage as AppScormPackage;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use App\CourseProgress as AppCourseProgress;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        Log::info("Chapter SCORM Upload Start");

        $request->validate([
            'course_id'    => 'required|integer',
            'chapter_name' => 'required|string',
            'zip_file'     => 'required|mimes:zip|max:1024000',
        ]);

        Log::info("Chapter Validation Passed");

        $zip = $request->file('zip_file');
        $folderName = 'chapter_scorm_' . time();
        $extractPath = public_path('scorm_packages/' . $folderName);

        File::makeDirectory($extractPath, 0755, true);

        $zipPath = $extractPath . '/' . $zip->getClientOriginalName();
        $zip->move($extractPath, $zip->getClientOriginalName());

        Log::info("Zip moved to: " . $zipPath);

        $zipArchive = new \ZipArchive;
        if ($zipArchive->open($zipPath)) {
            $zipArchive->extractTo($extractPath);
            $zipArchive->close();
            unlink($zipPath);
            Log::info("Zip extracted successfully");
        } else {
            Log::error("Zip could not be opened");
            return back()->with('error', 'Zip extraction failed.');
        }

        $manifestPath = $this->findManifest($extractPath);
        Log::info("Manifest path: " . $manifestPath);

        if (!$manifestPath || !file_exists($manifestPath)) {
            Log::error("imsmanifest.xml not found");
            return back()->with('error', 'SCORM manifest not found.');
        }

        $launchFile = null;
        $xml = simplexml_load_file($manifestPath);
        $xml->registerXPathNamespace('ns', 'http://www.imsproject.org/xsd/imscp_rootv1p1p2');
        $resource = $xml->xpath('//ns:resource')[0] ?? null;

        if ($resource) {
            $base = (string) ($resource['base'] ?? '');
            $href = (string) $resource['href'];
            $launchFile = $base ? ($base . '/' . $href) : $href;
        }

        $launchFullPath = dirname($manifestPath) . '/' . $launchFile;

        if (!$launchFile || !file_exists($launchFullPath)) {
            Log::error("Launch file missing: " . $launchFullPath);
            return back()->with('error', 'Launch file not found.');
        }

        Chapter::create([
            'course_id'   => $request->course_id,
            'name'        => $request->chapter_name,
            'folder_name' => $folderName,
            'launch_file' => str_replace($extractPath . '/', '', $launchFullPath),
        ]);

        Log::info("Chapter SCORM uploaded successfully");

        return redirect()
            ->route('courses.index')
            ->with('success', 'Chapter created successfully.');
    }



    public function uploadChapterManual(Request $request)
    {

        // Content types that are chapter-level (once per chapter)
        $chapterLevelTypes = [
            'glossary' => 'Glossary',
            'infographics' => 'Infographics',
            'textbook' => 'Textbook',
            'map' => 'Map',
        ];

        // Content types that are lesson-level (multiple per chapter)
        $lessonLevelTypes = [
            'detailed_trainer_slides' => 'Detailed Trainer Slides',
            'summary_slides' => 'Summary Slides',
            'videos' => 'Videos',
        ];

        // Build validation rules
        $rules = [
            'course_id' => 'required|integer',
            'chapter_name' => 'required|string',
            'lessons' => 'required|array|min:1',
            'lessons.*.lesson_name' => 'required|string',
        ];

        // Chapter-level content validation
        foreach ($chapterLevelTypes as $key => $label) {
            $rules["manual_{$key}"] = 'nullable|array';
            $rules["manual_{$key}.*"] = 'nullable|file|max:204800';
        }

        // Lesson-level content validation
        foreach ($lessonLevelTypes as $key => $label) {
            $rules["lessons.*.manual_{$key}"] = 'nullable|array';
            $rules["lessons.*.manual_{$key}.*"] = 'nullable|file|max:204800';
        }

        try {
            $validated = $request->validate($rules);
            Log::info('Validation Passed');
            Log::info('Validated Data Structure:', [
                'course_id' => $validated['course_id'],
                'chapter_name' => $validated['chapter_name'],
                'lessons_count' => count($validated['lessons']),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Failed:', [
                'errors' => $e->errors(),
                'messages' => $e->getMessage()
            ]);
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $chapter = Chapter::create([
                'course_id' => $validated['course_id'],
                'name' => $validated['chapter_name'],
            ]);


            $chapterFileCount = 0;

            foreach ($chapterLevelTypes as $key => $label) {
                $fieldName = "manual_{$key}";

                if ($request->hasFile($fieldName)) {
                    $files = $request->file($fieldName);

                    // Ensure files is an array
                    $filesArray = is_array($files) ? $files : [$files];

                    foreach ($filesArray as $fileIndex => $file) {
                        if (!$file || !$file->isValid()) {
                            continue;
                        }

                        $this->saveManualFile($file, $chapter, null, $key, $label);
                        $chapterFileCount++;
                    }
                } else {
                    Log::info("No files for chapter-level {$label}");
                }
            }


            $lessonOrder = 1;
            $lessonFileCount = 0;
            $totalLessons = 0;

            foreach ($validated['lessons'] as $index => $lessonData) {

                // Create lesson
                $lesson = Lesson::create([
                    'chapter_id' => $chapter->id,
                    'lesson_name' => $lessonData['lesson_name'],
                    'lesson_order' => $lessonOrder++,
                ]);

                $totalLessons++;

                // Upload lesson-level content (detailed slides, summary, videos)
                foreach ($lessonLevelTypes as $key => $label) {
                    $fieldName = "manual_{$key}";
                    $fullFieldName = "lessons.{$index}.{$fieldName}";

                    if ($request->hasFile($fullFieldName)) {
                        $files = $request->file($fullFieldName);

                        // Ensure it's an array
                        $filesArray = is_array($files) ? $files : [$files];



                        foreach ($filesArray as $fileIndex => $file) {
                            if ($file && $file->isValid()) {
                                $this->saveManualFile($file, $chapter, $lesson, $key, $label);
                                $lessonFileCount++;
                            } else {
                                Log::warning("Invalid lesson file", [
                                    'lesson_id' => $lesson->id,
                                    'file_index' => $fileIndex
                                ]);
                            }
                        }
                    } else {
                        Log::info("No files for lesson content", [
                            'lesson_id' => $lesson->id,
                            'field' => $fullFieldName
                        ]);
                    }
                }
            }


            $totalFiles = $chapterFileCount + $lessonFileCount;

            if ($totalFiles === 0) {
                DB::rollBack();
                Log::warning('No files uploaded - rolling back');
                return back()->with('error', 'Please upload at least one file for manual content.');
            }

            DB::commit();

            return redirect()
                ->route('chapters')
                ->with('success', "Manual chapter uploaded successfully with {$totalLessons} lesson(s) and {$totalFiles} file(s).");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('CHAPTER MANUAL UPLOAD FAILED', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Failed to upload chapter: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Helper method to save manual files
     */
    private function saveManualFile($file, $chapter, $lesson, $contentKey, $contentLabel)
    {
        try {
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();

            if ($lesson) {
                $pathSegment = "lesson_{$lesson->id}/{$contentKey}";
            } else {
                $pathSegment = "chapter_level/{$contentKey}";
            }

            $storagePath = "private/manual_uploads/{$chapter->id}/{$pathSegment}";

            // Generate unique filename
            $filename = uniqid() . '_' . time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);

            $file->storeAs($storagePath, $filename);

            $path = "manual_uploads/{$chapter->id}/{$pathSegment}/{$filename}";

            // Save to database
            ChapterManualContent::create([
                'chapter_id' => $chapter->id,
                'lesson_id' => $lesson ? $lesson->id : null,
                'content_type' => $contentLabel,
                'file_path' => $path, // IMPORTANT
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to save file', [
                'error' => $e->getMessage(),
                'file' => $originalName ?? 'unknown',
                'chapter_id' => $chapter->id,
                'lesson_id' => $lesson ? $lesson->id : null,
                'line' => $e->getLine(),
            ]);
            throw $e;
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

    public function deleteManualContent(ChapterManualContent $content)
    {
        // optional: also delete file from storage
        // Storage::delete('private/'.$content->file_path); // adjust path if needed
        $content->delete();

        return back()->with('success', 'Content deleted.');
    }

    public function updateChapterManual(Request $request, Chapter $chapter)
    {
        // same maps as upload
        $chapterLevelTypes = [
            'glossary' => 'Glossary',
            'infographics' => 'Infographics',
            'textbook' => 'Textbook',
            'map' => 'Map',
        ];

        $lessonLevelTypes = [
            'detailed_trainer_slides' => 'Detailed Trainer Slides',
            'summary_slides' => 'Summary Slides',
            'videos' => 'Videos',
        ];

        $rules = [
            'chapter_name' => 'required|string',
            'lessons' => 'nullable|array',
            'lessons.*.lesson_id' => 'nullable|integer',
            'lessons.*.lesson_name' => 'required|string',
        ];

        foreach ($chapterLevelTypes as $key => $label) {
            $rules["manual_{$key}"] = 'nullable|array';
            $rules["manual_{$key}.*"] = 'nullable|file|max:204800';
        }

        foreach ($lessonLevelTypes as $key => $label) {
            $rules["lessons.*.manual_{$key}"] = 'nullable|array';
            $rules["lessons.*.manual_{$key}.*"] = 'nullable|file|max:204800';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $chapter->update([
                'name' => $validated['chapter_name'],
            ]);

            foreach ($chapterLevelTypes as $key => $label) {
                $fieldName = "manual_{$key}";
                if ($request->hasFile($fieldName)) {
                    $files = $request->file($fieldName);
                    $filesArray = is_array($files) ? $files : [$files];

                    foreach ($filesArray as $file) {
                        if ($file && $file->isValid()) {
                            $this->saveManualFile($file, $chapter, null, $key, $label);
                        }
                    }
                }
            }

            $lessonFiles = $request->file('lessons', []);
            $lessonsInput = $validated['lessons'] ?? [];

            foreach ($lessonsInput as $index => $lessonData) {

                // existing lesson or create new
                if (!empty($lessonData['lesson_id'])) {
                    $lesson = Lesson::where('chapter_id', $chapter->id)
                        ->where('id', $lessonData['lesson_id'])
                        ->firstOrFail();

                    $lesson->update(['lesson_name' => $lessonData['lesson_name']]);
                } else {
                    $maxOrder = (int) Lesson::where('chapter_id', $chapter->id)->max('lesson_order');
                    $lesson = Lesson::create([
                        'chapter_id' => $chapter->id,
                        'lesson_name' => $lessonData['lesson_name'],
                        'lesson_order' => $maxOrder + 1,
                    ]);
                }

                // files attach
                foreach ($lessonLevelTypes as $key => $label) {
                    $fieldName = "manual_{$key}";

                    if (isset($lessonFiles[$index][$fieldName])) {
                        $files = $lessonFiles[$index][$fieldName];
                        $filesArray = is_array($files) ? $files : [$files];

                        foreach ($filesArray as $file) {
                            if ($file && $file->isValid()) {
                                $this->saveManualFile($file, $chapter, $lesson, $key, $label);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()
    ->route('chapters')
    ->with('success', 'Chapter manual updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
    public function chapterindex(Builder $builder)
    {
        if (request()->ajax()) {

            $query = Chapter::join('scorm_packages', 'chapters.course_id', '=', 'scorm_packages.id')
                ->leftJoin('lessons', 'chapters.id', '=', 'lessons.chapter_id')
                ->select(
                    'chapters.*',
                    'scorm_packages.title as course_title',
                    DB::raw('COUNT(DISTINCT lessons.id) as lesson_count')
                )
                ->groupBy(
                    'chapters.id',
                    'scorm_packages.title',
                    'chapters.name',
                    'chapters.course_id',
                    'chapters.created_at',
                    'chapters.updated_at',
                    'chapters.deleted_at',
                    'chapters.chapter_order',
                    'chapters.chap_imp_id',
                    'chapters.sat_total_time',
                    'chapters.status',
                    'chapters.created_by',
                    'chapters.updated_by',
                    'chapters.level_id',
                    'chapters.subject_id',
                    'chapters.folder_name',
                    'chapters.launch_file'
                );

            return DataTables::of($query)
                ->addColumn('course_title', fn($chapter) => $chapter->course_title)
                ->addColumn('chapter_name', fn($chapter) => $chapter->name)
                ->addColumn('lessons', function ($chapter) {
                    return $chapter->lesson_count > 0
                        ? '<span class="badge badge-info">' . $chapter->lesson_count . ' Lesson(s)</span>'
                        : '<span class="badge badge-secondary">No Lessons</span>';
                })
                ->addColumn('action', function ($chapter) {
                    $editUrl = route('chapter.manual.edit', $chapter->id);
                    // $viewUrl = route('view.chapter', $chapter->id); // तुझा existing view route असेल
                    return '
                        <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                        ';
                })
                ->rawColumns(['course_title', 'chapter_name', 'lessons', 'action'])
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
            [
                'data'  => 'lessons',
                'name'  => 'lesson_count',
                'title' => 'Lessons',
                'orderable' => false,
                'searchable' => false
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

        return view('courses.chapterlist', compact('html'));
    }

    public function editChapterManual(Chapter $chapter)
    {
        $chapter->load(['lessons', 'manualContents']); // relationship names as per your models

        // lesson wise group
        $lessonContents = $chapter->manualContents
            ->whereNotNull('lesson_id')
            ->groupBy('lesson_id');

        // chapter-level
        $chapterContents = $chapter->manualContents
            ->whereNull('lesson_id')
            ->groupBy('content_type');

        return view('courses.chapter_manual_edit', compact(
            'chapter',
            'lessonContents',
            'chapterContents'
        ));
    }

    public function chapterList($courseId)
    {
        // dd($courseId);
        $userId = auth()->id();
        $chapters = Chapter::where('course_id', $courseId)->get();

        // Fetch progress data for each chapter
        foreach ($chapters as $chapter) {
            $progress = \App\CourseProgress::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('chapter_id', $chapter->id)
                ->first();

            $chapter->progress_percent = $progress->progress_percent ?? 0;
        }

        return view('chapters.list', [
            'chapters' => $chapters,
            'courseId' => $courseId
        ]);
    }

    public function viewChapter($id)
    {
        $chapter = Chapter::with('manualContents')->findOrFail($id);
        $userId = auth()->id();

        // Get user's progress for this chapter
        $courseId = request()->get('course') ?? $chapter->course_id;
        $progress = AppCourseProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('chapter_id', $chapter->id)
            ->first();

        if ($chapter->manualContents->isNotEmpty() && empty($chapter->launch_file)) {

            $overviewTypes = [
                'Glossary',
                'Infographics',
                'Textbook',
                'Map',
            ];

            $lessonTypes = [
                'Detailed Trainer Slides',
                'Summary Slides',
                'Videos',
            ];

            //  Overview contents (SORTED BY ID)
            $overview = $chapter->manualContents
                ->whereNull('lesson_id')
                ->whereIn('content_type', $overviewTypes)
                ->sortBy('id')
                ->groupBy('content_type')
                ->map(function ($items) {
                    return $items->map(function ($content) {
                        return [
                            'id' => $content->id,
                            'label' => $content->content_type,
                            'url' => route('pdf.stream', $content->file_path),
                            'original_name' => $content->original_name ?? basename($content->file_path),
                            'size' => $content->file_size,
                        ];
                    });
                });

            //  Lessons contents (SORTED BY ID)
            $lessons = $chapter->manualContents
                ->whereNotNull('lesson_id')
                ->whereIn('content_type', $lessonTypes)
                ->sortBy('id')
                ->groupBy('lesson_id')
                ->map(function ($lessonItems) {

                    $lesson = \App\Models\Lesson::find($lessonItems->first()->lesson_id);

                    return [
                        'lesson_id'   => $lesson->id,
                        'lesson_name' => $lesson->lesson_name,
                        'contents' => $lessonItems
                            ->sortBy('id')   //  Added Sorting inside lesson
                            ->groupBy('content_type')
                            ->map(function ($items) {
                                return $items->map(function ($content) {
                                    return [
                                        'id' => $content->id,
                                        'url' => route('secure.file', $content->file_path),
                                        'original_name' => $content->original_name ?? basename($content->file_path),
                                        'size' => $content->file_size,
                                    ];
                                });
                            }),
                    ];
                })
                ->values();
            //  dd($lessons);
            return view('chapters.manual', [
                'chapter' => $chapter,
                'overview' => $overview,
                'lessons' => $lessons,
                'userProgress' => $progress ? $progress->progress_percent : 0,
            ]);
        }

        // ---------- SCORM PART (UNCHANGED) ----------
        $userId = auth()->id();

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
            'chapterId'    => $chapter->id,
            'resumeTime'   => $progress->resume_from_time ?? 0,
            'lastLocation' => $progress->cmi_core_lesson_location ?? '',
            'lessonStatus' => $progress->cmi_core_lesson_status ?? '',
            'score'        => $progress->cmi_core_score_raw ?? '',
            'suspendData'  => $progress->progress_data['suspend_data'] ?? '',
        ]);
    }



    public function getChapters($courseId)
    {
        $userId = auth()->id();

        $chapters = Chapter::where('course_id', $courseId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($chapter) use ($userId, $courseId) {

                // First, check if there's manual chapter progress saved
                $progress = AppCourseProgress::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->where('chapter_id', $chapter->id)
                    ->first();

                // Check if chapter has manual content (no launch_file)
                $hasManualContent = $chapter->manualContents()->count() > 0 && empty($chapter->launch_file);

                // If manual content exists and progress is recorded (even if 0%), use it
                if ($hasManualContent && $progress) {
                    $chapter->progress_percent = (int) $progress->progress_percent;
                    return $chapter;
                }

                // If manual content exists but no progress yet, set to 0
                if ($hasManualContent && !$progress) {
                    $chapter->progress_percent = 0;
                    return $chapter;
                }

                // Otherwise calculate from SCORM session time
                $allProgress = AppCourseProgress::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->whereNotNull('chapter_id')
                    ->get();

                $chapterProgress = $allProgress->where('chapter_id', $chapter->id);

                $totalSessionTime = (int) $chapterProgress->sum('session_time');

                // duration in seconds
                $duration = (!empty($chapter->watch_time) && $chapter->watch_time > 0)
                    ? (int) $chapter->watch_time
                    : 30 * 60;

                if ($totalSessionTime > 0 && $duration > 0) {

                    $rawPercent = ($totalSessionTime / $duration) * 80;

                    $percent = max(1, floor($rawPercent));
                } else {
                    $percent = 0;
                }

                // cap at 80
                if ($percent > 80) {
                    $percent = 80;
                }

                // SCORM completion
                $isCompleted = $chapterProgress
                    ->whereIn('cmi_core_lesson_status', ['completed', 'passed'])
                    ->isNotEmpty();

                if ($isCompleted) {
                    $percent = 100;
                }

                $chapter->progress_percent = $percent;
                $chapter->is_completed = $isCompleted;

                return $chapter;
            });

        return view('chapters.list', compact('chapters', 'courseId'));
    }

    public function autoLoginChapter(Request $request, $courseId)
    {
        $uid   = $request->uid;
        $token = $request->token;

        if (!$uid || !$token) {
            abort(403, 'Unauthorized access');
        }

        // user verify
        $user = User::where('id', $uid)
            ->where('api_token', $token)
            ->first();

        if (!$user) {
            abort(403, 'Invalid user or token');
        }

        Auth::login($user);

        // direct chapters page par redirect
        return redirect('/course/' . $courseId . '/chapters');
    }

    public function stream($path)
    {
        abort_unless(auth()->check(), 403);

        $fullPath = storage_path('app/private/' . $path);

        abort_unless(file_exists($fullPath), 404);

        return response()->stream(function () use ($fullPath) {
            readfile($fullPath);
        }, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Accept-Ranges'       => 'bytes',
            'Cache-Control'       => 'private, no-store, no-cache',
        ]);
    }
}
