<?php

namespace App\Http\Controllers;

use App\Assignedcourse;
use App\ChapterButtonClick;
use App\ChapterRoundView;
use App\CourseProgress as AppCourseProgress;
use App\CourseProgress;
use App\CourseView;
use App\EmailTemplate;
use App\Models\Chapter;
use App\Models\ChapterManualContent;
use App\Models\EmailLog;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use App\ScormPackage as AppScormPackage;
use App\Services\ScormCloudService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;
use ZipArchive;

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
            'watch_time' => 'required|integer|min:1|max:3000',
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
            'chapter_name' => [
                'required',
                'string',
                Rule::unique('chapters', 'name')
                    ->where('course_id', $request->course_id)
                    ->where('subject_id', $request->subject_id)
            ],
            'subject_id'   => 'required|integer',
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
            'subject_id'  => $request->subject_id,
            'launch_file' => str_replace($extractPath . '/', '', $launchFullPath),
        ]);

        Log::info("Chapter SCORM uploaded successfully");

        //  Send mail to all users of this course (content upload)

        Log::info('CONTENT UPLOAD MAIL PROCESS START', [
            'course_id' => $request->course_id
        ]);

        $template = EmailTemplate::where('name', 'course_content_upload')
            ->where('status', 1)
            ->first();

        if (!$template) {
            Log::warning('Email template not found or inactive', [
                'template_name' => 'course_content_upload'
            ]);
            return;
        }

        Log::info('Email template found', [
            'subject' => $template->subject
        ]);

        $loginUrl = config('app.url') . '/login';

        // Is course ke sab users nikaalo
        $users = AssignedCourse::where('course_id', $request->course_id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        Log::info('Total users found for this course', [
            'count' => $users->count()
        ]);

        foreach ($users as $user) {

            Log::info('Preparing mail for user', [
                'user_id' => $user->id,
                'email'   => $user->email
            ]);

            $body = str_replace(
                ['{{name}}', '{{login_url}}'],
                [$user->name, $loginUrl],
                $template->body
            );

            try {

                Mail::send([], [], function ($message) use ($user, $template, $body) {
                    $message->to($user->email)
                        ->subject($template->subject)
                        ->setBody($body, 'text/html');

                    if ($template->cc) {
                        $message->cc(explode(',', $template->cc));
                    }

                    if ($template->bcc) {
                        $message->bcc(explode(',', $template->bcc));
                    }
                });

                Log::info('Mail SENT SUCCESS', [
                    'email' => $user->email
                ]);

                EmailLog::create([
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'subject' => $template->subject,
                    'body'    => $body,
                    'status'  => 'sent',
                    'cc'      => $template->cc,
                    'bcc'     => $template->bcc,
                ]);
            } catch (\Exception $e) {

                Log::error('Mail FAILED for user', [
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);

                EmailLog::create([
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'subject' => $template->subject,
                    'body'    => $body,
                    'status'  => 'failed',
                    'error_message' => $e->getMessage(),
                    'cc'      => $template->cc,
                    'bcc'     => $template->bcc,
                ]);
            }
        }

        Log::info('CONTENT UPLOAD MAIL PROCESS END');

        return redirect()
            ->route('chapters')
            ->with('success', 'Chapter created successfully.');
    }



    public function uploadChapterManual(Request $request)
    { // Content types that are chapter-level (once per chapter)
        $chapterLevelTypes = [
            'glossary'     => 'Glossary',
            'infographics' => 'Infographics',
            'textbook'     => 'Textbook',
            'map'          => 'Map',
        ];

        // Content types that are lesson-level (multiple per chapter)
        $lessonLevelTypes = [
            'detailed_trainer_slides' => 'Detailed Trainer Slides',
            'summary_slides'          => 'Summary Slides',
            'videos'                  => 'Videos',
        ];

        // ── Build validation rules ──
        $rules = [
            'course_id'              => 'required|integer',
            'subject_id'             => 'required|integer',   // ← FIX: was missing
            'chapter_name'           => [
                'required',
                'string',
                Rule::unique('chapters', 'name')
                    ->where('course_id',  $request->course_id)
                    ->where('subject_id', $request->subject_id)
            ],
            'lessons'                => 'required|array|min:1',
            'lessons.*.lesson_name'  => 'required|string',
        ];

        // Chapter-level content validation
        foreach ($chapterLevelTypes as $key => $label) {
            $rules["manual_{$key}"]    = 'nullable|array';
            $rules["manual_{$key}.*"]  = 'nullable|file|max:204800';
        }

        // Lesson-level content validation
        foreach ($lessonLevelTypes as $key => $label) {
            $rules["lessons.*.manual_{$key}"]    = 'nullable|array';
            $rules["lessons.*.manual_{$key}.*"]  = 'nullable|file|max:204800';
        }

        try {
            $validated = $request->validate($rules);
            Log::info('Validation Passed');
            Log::info('Validated Data Structure:', [
                'course_id'    => $validated['course_id'],
                'subject_id'   => $validated['subject_id'],
                'chapter_name' => $validated['chapter_name'],
                'lessons_count' => count($validated['lessons']),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Failed:', [
                'errors'   => $e->errors(),
                'messages' => $e->getMessage()
            ]);
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $chapter = Chapter::create([
                'course_id'  => $validated['course_id'],
                'subject_id' => $validated['subject_id'],   // ← now safely from $validated
                'name'       => $validated['chapter_name'],
            ]);

            $chapterFileCount = 0;

            foreach ($chapterLevelTypes as $key => $label) {
                $fieldName = "manual_{$key}";

                if ($request->hasFile($fieldName)) {
                    $files      = $request->file($fieldName);
                    $filesArray = is_array($files) ? $files : [$files];

                    foreach ($filesArray as $fileIndex => $file) {
                        if (!$file || !$file->isValid()) continue;
                        $this->saveManualFile($file, $chapter, null, $key, $label);
                        $chapterFileCount++;
                    }
                } else {
                    Log::info("No files for chapter-level {$label}");
                }
            }

            $lessonOrder    = 1;
            $lessonFileCount = 0;
            $totalLessons   = 0;

            foreach ($validated['lessons'] as $index => $lessonData) {

                $lesson = Lesson::create([
                    'chapter_id'   => $chapter->id,
                    'lesson_name'  => $lessonData['lesson_name'],
                    'lesson_order' => $lessonOrder++,
                ]);

                $totalLessons++;

                foreach ($lessonLevelTypes as $key => $label) {
                    $fieldName     = "manual_{$key}";
                    $fullFieldName = "lessons.{$index}.{$fieldName}";

                    if ($request->hasFile($fullFieldName)) {
                        $files      = $request->file($fullFieldName);
                        $filesArray = is_array($files) ? $files : [$files];

                        foreach ($filesArray as $fileIndex => $file) {
                            if ($file && $file->isValid()) {
                                $this->saveManualFile($file, $chapter, $lesson, $key, $label);
                                $lessonFileCount++;
                            } else {
                                Log::warning("Invalid lesson file", [
                                    'lesson_id'  => $lesson->id,
                                    'file_index' => $fileIndex
                                ]);
                            }
                        }
                    } else {
                        Log::info("No files for lesson content", [
                            'lesson_id' => $lesson->id,
                            'field'     => $fullFieldName
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


            //  Send mail to all users of this course (content upload)

            Log::info('CONTENT UPLOAD MAIL PROCESS START', [
                'course_id' => $request->course_id
            ]);

            $template = EmailTemplate::where('name', 'course_content_upload')
                ->where('status', 1)
                ->first();

            if (!$template) {
                Log::warning('Email template not found or inactive', [
                    'template_name' => 'course_content_upload'
                ]);
                return;
            }

            Log::info('Email template found', [
                'subject' => $template->subject
            ]);

            $loginUrl = config('app.url') . '/login';

            // Is course ke sab users nikaalo
            $users = Assignedcourse::where('course_id', $request->course_id)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            Log::info('Total users found for this course', [
                'count' => $users->count()
            ]);

            foreach ($users as $user) {

                Log::info('Preparing mail for user', [
                    'user_id' => $user->id,
                    'email'   => $user->email
                ]);

                $body = str_replace(
                    ['{{name}}', '{{login_url}}'],
                    [$user->name, $loginUrl],
                    $template->body
                );

                try {

                    Mail::send([], [], function ($message) use ($user, $template, $body) {
                        $message->to($user->email)
                            ->subject($template->subject)
                            ->setBody($body, 'text/html');

                        if ($template->cc) {
                            $message->cc(explode(',', $template->cc));
                        }

                        if ($template->bcc) {
                            $message->bcc(explode(',', $template->bcc));
                        }
                    });

                    Log::info('Mail SENT SUCCESS', [
                        'email' => $user->email
                    ]);

                    EmailLog::create([
                        'user_id' => $user->id,
                        'email'   => $user->email,
                        'subject' => $template->subject,
                        'body'    => $body,
                        'status'  => 'sent',
                        'cc'      => $template->cc,
                        'bcc'     => $template->bcc,
                    ]);
                } catch (\Exception $e) {

                    Log::error('Mail FAILED for user', [
                        'email' => $user->email,
                        'error' => $e->getMessage()
                    ]);

                    EmailLog::create([
                        'user_id' => $user->id,
                        'email'   => $user->email,
                        'subject' => $template->subject,
                        'body'    => $body,
                        'status'  => 'failed',
                        'error_message' => $e->getMessage(),
                        'cc'      => $template->cc,
                        'bcc'     => $template->bcc,
                    ]);
                }
            }

            Log::info('CONTENT UPLOAD MAIL PROCESS END');

            return redirect()
                ->route('chapters')
                ->with('success', "Manual chapter uploaded successfully with {$totalLessons} lesson(s) and {$totalFiles} file(s).");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('CHAPTER MANUAL UPLOAD FAILED', [
                'error_message' => $e->getMessage(),
                'error_file'    => $e->getFile(),
                'error_line'    => $e->getLine(),
                'trace'         => $e->getTraceAsString()
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
        // $courseView = CourseView::where('user_id', $userId)
        //     ->where('course_id', $id)
        //     ->first();

        // if (!$courseView) {
        //     $courseView = CourseView::create([
        //         'user_id' => $userId,
        //         'course_id' => $id,
        //         'view_limit' => 1,
        //     ]);
        // }

        $totalSessionTime = AppCourseProgress::where('user_id', $userId)
            ->where('course_id', $id)
            ->sum('session_time');

        // Formula: totalSessionTime - ((attempt_no - 1) * watchTime)
        // $currentAttemptTime = max(0, $totalSessionTime - (($courseView->view_limit - 1) * $watchTime));

        // if ($currentAttemptTime >= $watchTime) {
        //     $courseView->increment('view_limit');
        //     $courseView->update(['last_reset_time' => now()]);
        // }

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

    public function deleteLesson(Lesson $lesson)
    {
        DB::beginTransaction();

        try {
            $contents = ChapterManualContent::where('lesson_id', $lesson->id)->get();

            foreach ($contents as $content) {

                if (\Storage::exists($content->file_path)) {
                    \Storage::delete($content->file_path);
                }

                $content->delete();
            }

            $chapterId = $lesson->chapter_id;

            $lesson->delete();

            $lessons = \App\Models\Lesson::where('chapter_id', $chapterId)
                ->orderBy('lesson_order')
                ->get();

            foreach ($lessons as $index => $l) {
                $l->update(['lesson_order' => $index + 1]);
            }

            DB::commit();

            return back()->with('success', 'Lesson and all its files deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateChapterManual(Request $request, Chapter $chapter)
    {
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
            'chapter_name' => [
                'required',
                'string',
                Rule::unique('chapters', 'name')
                    ->where('course_id', $chapter->course_id)
                    ->where('subject_id', $chapter->subject_id)
                    ->ignore($chapter->id),
            ],
            'lessons' => 'nullable|array',
            'lessons.*.lesson_id' => 'nullable|integer',
            'lessons.*.lesson_name' => 'nullable|string',
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
                if ($request->hasFile("manual_{$key}")) {
                    foreach ($request->file("manual_{$key}") as $file) {
                        if ($file && $file->isValid()) {
                            $this->saveManualFile($file, $chapter, null, $key, $label);
                        }
                    }
                }
            }

            $lessonsInput = $validated['lessons'] ?? [];

            foreach ($lessonsInput as $index => $lessonData) {

                if (empty($lessonData['lesson_name']) && empty($lessonData['lesson_id'])) {
                    continue;
                }

                if (!empty($lessonData['lesson_id'])) {
                    $lesson = Lesson::where('chapter_id', $chapter->id)
                        ->where('id', $lessonData['lesson_id'])
                        ->first();

                    if (!$lesson) {
                        continue;
                    }

                    $lesson->update([
                        'lesson_name' => $lessonData['lesson_name'],
                    ]);
                } else {
                    $maxOrder = (int) Lesson::where('chapter_id', $chapter->id)->max('lesson_order');

                    $lesson = Lesson::create([
                        'chapter_id' => $chapter->id,
                        'lesson_name' => $lessonData['lesson_name'],
                        'lesson_order' => $maxOrder + 1,
                    ]);
                }

                foreach ($lessonLevelTypes as $key => $label) {

                    $files = $request->file("lessons.$index.manual_{$key}");

                    if ($files) {
                        foreach ($files as $file) {
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
                ->leftJoin('lessons', function ($join) {
                    $join->on('chapters.id', '=', 'lessons.chapter_id')
                        ->whereNull('lessons.deleted_at');
                })
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

                    if (!empty($chapter->launch_file) || !empty($chapter->folder_name)) {
                        // SCORM chapter
                        $editUrl = route('chapter.scorm.edit', $chapter->id);
                    } else {
                        // Manual chapter
                        $editUrl = route('chapter.manual.edit', $chapter->id);
                    }

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

    public function scormEdit($id)
    {
        $chapter = Chapter::with('subject')->findOrFail($id);

        if (empty($chapter->folder_name)) {
            return redirect()->route('chapter.manual.edit', $id)
                ->with('error', 'This is not a SCORM chapter.');
        }

        $scormPath = asset('scorm_packages/' . $chapter->folder_name . '/' . $chapter->launch_file);

        $courses = AppScormPackage::all();

        // subjects of selected course
        $subjects = Subject::where('course_id', $chapter->course_id ?? null)->get();

        return view('courses.scorm_edit', compact('chapter', 'scormPath', 'courses', 'subjects'));
    }


    public function updateScormChapter(Request $request, $id)
    {
        $chapter = Chapter::findOrFail($id);

        $request->validate([
            'course_id'    => 'required|integer',
            'subject_id'   => 'required|integer',
            'chapter_name' => 'required|string',
            'zip_file'     => 'nullable|mimes:zip|max:1024000',
        ]);

        // update basic fields
        $chapter->course_id = $request->course_id;
        $chapter->subject_id = $request->subject_id;
        $chapter->name = $request->chapter_name;

        /* ───── ZIP REPLACE ONLY IF UPLOADED ───── */
        if ($request->hasFile('zip_file')) {

            // delete old folder
            if (!empty($chapter->folder_name)) {
                $oldPath = public_path('scorm_packages/' . $chapter->folder_name);
                if (File::exists($oldPath)) {
                    File::deleteDirectory($oldPath);
                }
            }

            $zip = $request->file('zip_file');
            $folderName = 'chapter_scorm_' . time();
            $extractPath = public_path('scorm_packages/' . $folderName);

            File::makeDirectory($extractPath, 0755, true);

            $zipName = $zip->getClientOriginalName();
            $zip->move($extractPath, $zipName);

            $zipPath = $extractPath . '/' . $zipName;

            $zipArchive = new \ZipArchive;
            if ($zipArchive->open($zipPath)) {
                $zipArchive->extractTo($extractPath);
                $zipArchive->close();
                unlink($zipPath);
            }

            /* ─── FIND MANIFEST ─── */
            $manifestPath = $this->findManifest($extractPath);

            if (!$manifestPath || !file_exists($manifestPath)) {
                return back()->with('error', 'Invalid SCORM package.');
            }

            $xml = simplexml_load_file($manifestPath);
            $xml->registerXPathNamespace('ns', 'http://www.imsproject.org/xsd/imscp_rootv1p1p2');
            $resource = $xml->xpath('//ns:resource')[0] ?? null;

            $launchFile = null;

            if ($resource) {
                $base = (string) ($resource['base'] ?? '');
                $href = (string) $resource['href'];
                $launchFile = $base ? ($base . '/' . $href) : $href;
            }

            $chapter->folder_name = $folderName;
            $chapter->launch_file = $launchFile;
        }

        $chapter->save();

        return redirect()->route('chapters')->with('success', 'SCORM Chapter updated successfully.');
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
    $chapter = Chapter::with(['manualContents', 'lessons'])->findOrFail($id);
    $userId = auth()->id();
    $courseId = request()->get('scorm_packages') ?? $chapter->course_id;

  // Is specific chapter ka last round nikalo (NOT course ka)
$lastRoundOfThisChapter = ChapterRoundView::where('user_id', $userId)
    ->where('course_id', $courseId)
    ->where('chapter_id', $chapter->id)
    ->max('round_no');

$currentRound = $lastRoundOfThisChapter ? $lastRoundOfThisChapter + 1 : 1;

ChapterRoundView::create([
    'user_id'    => $userId,
    'course_id'  => $courseId,
    'chapter_id' => $chapter->id,
    'round_no'   => $currentRound,
]);

    $progress = AppCourseProgress::where('user_id', $userId)
        ->where('course_id', $courseId)
        ->where('chapter_id', $chapter->id)
        ->first();

    if ($chapter->manualContents->isNotEmpty() && empty($chapter->launch_file)) {

        $overviewTypes = ['Glossary','Infographics','Textbook','Map'];
        $lessonTypes = ['Detailed Trainer Slides','Summary Slides','Videos'];

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

        $lessons = $chapter->lessons->map(function ($lesson) use ($chapter, $lessonTypes) {

            $lessonItems = $chapter->manualContents
                ->where('lesson_id', $lesson->id)
                ->whereIn('content_type', $lessonTypes)
                ->sortBy('id');

            return [
                'lesson_id'   => $lesson->id,
                'lesson_name' => $lesson->lesson_name,
                'contents' => $lessonItems
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
        })->values();

        return view('chapters.manual', [
            'chapter' => $chapter,
            'overview' => $overview,
            'lessons' => $lessons,
            'userProgress' => $progress ? $progress->progress_percent : 0,
        ]);
    }

    // ===== SCORM VIEW PART =====
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

public function incrementAttempt(Request $request)
{
    $userId   = auth()->id();
    $courseId = $request->course_id;

    $course = AppScormPackage::findOrFail($courseId);

    $totalChapters = Chapter::where('course_id', $courseId)->count();

    // Get completed rounds
    $completedRounds = ChapterRoundView::where('user_id', $userId)
        ->where('course_id', $courseId)
        ->select('round_no')
        ->groupBy('round_no')
        ->havingRaw('COUNT(DISTINCT chapter_id) = ?', [$totalChapters])
        ->pluck('round_no');

    $attempt = $completedRounds->count();

    $courseView = CourseView::updateOrCreate(
        [
            'user_id'   => $userId,
            'course_id' => $courseId,
        ],
        [
            'view_limit' => $attempt,
        ]
    );

    return response()->json([
        'success' => true,
        'attempt' => $attempt
    ]);
}

    public function getChapters($courseId)
    {
        $userId = auth()->id();

        $course = AppScormPackage::find($courseId);
        $attemptLimit = (int) ($course->view_limit ?? 0);

        $chapters = Chapter::where('course_id', $courseId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($chapter) use ($userId, $courseId, $attemptLimit) {

                //  Chapter wise attempt check
                $chapterClicks = ChapterButtonClick::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->where('chapter_id', $chapter->id)
                    ->sum('click_count');

                $isChapterLocked = $attemptLimit > 0 && $chapterClicks >= $attemptLimit;

                // First, check if there's manual chapter progress saved
                $progress = AppCourseProgress::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->where('chapter_id', $chapter->id)
                    ->first();

                $hasManualContent = $chapter->manualContents()->count() > 0 && empty($chapter->launch_file);

                if ($hasManualContent && $progress) {
                    $chapter->progress_percent = (int) $progress->progress_percent;
                    $chapter->is_completed = $progress->progress_percent >= 100;
                    $chapter->is_locked = $isChapterLocked;
                    return $chapter;
                }

                if ($hasManualContent && !$progress) {
                    $chapter->progress_percent = 0;
                    $chapter->is_completed = false;
                    $chapter->is_locked = $isChapterLocked;
                    return $chapter;
                }

                $allProgress = AppCourseProgress::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->whereNotNull('chapter_id')
                    ->get();

                $chapterProgress = $allProgress->where('chapter_id', $chapter->id);

                $totalSessionTime = (int) $chapterProgress->sum('session_time');

                $duration = (!empty($chapter->watch_time) && $chapter->watch_time > 0)
                    ? (int) $chapter->watch_time
                    : 30 * 60;

                if ($totalSessionTime > 0 && $duration > 0) {
                    $rawPercent = ($totalSessionTime / $duration) * 80;
                    $percent = max(1, floor($rawPercent));
                } else {
                    $percent = 0;
                }

                if ($percent > 80) {
                    $percent = 80;
                }

                $isCompleted = $chapterProgress
                    ->whereIn('cmi_core_lesson_status', ['completed', 'passed'])
                    ->isNotEmpty();

                if ($isCompleted) {
                    $percent = 100;
                }

                $chapter->progress_percent = $percent;
                $chapter->is_completed = $isCompleted;

                //  Final lock only chapter wise
                $chapter->is_locked = $isChapterLocked;

                return $chapter;
            });

        $subjects = Subject::where('course_id', $courseId)->where('status', 1)->get();

        return view('chapters.list', compact('chapters', 'courseId', 'subjects'));
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

    public function recordClick(Request $request)
    {
        $validated = $request->validate([
            'chapter_id' => 'required|integer|exists:chapters,id',
            'course_id' => 'nullable|integer|exists:scorm_packages,id',
            'button_type' => 'required|in:start,resume,completed'
        ]);

        try {
            $click = ChapterButtonClick::recordClick(
                Auth::id(),
                $validated['chapter_id'],
                $validated['course_id'] ?? null,
                $validated['button_type']
            );

            return response()->json([
                'success' => true,
                'message' => 'Click recorded successfully',
                'data' => $click
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record click',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getClickStats(Request $request, $chapterId)
    {
        $userId = Auth::id();

        $stats = ChapterButtonClick::where('user_id', $userId)
            ->where('chapter_id', $chapterId)
            ->get()
            ->keyBy('button_type');

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getUserChapterStats(Request $request)
    {
        $userId = Auth::id();

        $stats = ChapterButtonClick::where('user_id', $userId)
            ->with('chapter:id,name')
            ->orderBy('last_clicked_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
