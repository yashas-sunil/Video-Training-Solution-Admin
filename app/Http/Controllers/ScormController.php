<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\CourseProgress;
use App\ScormPackage as AppScormPackage;
use ZipArchive;
use Illuminate\Http\Request;
use App\CourseProgress as AppCourseProgress;
use App\Services\ScormCloudService;
class ScormController extends Controller
{
     public function showForm()
    {
        return view('upload');
    }

public function upload(Request $request)
{
    Log::info(" SCORM Upload Start");

    $request->validate([
        'title' => 'required|string',
        'zip_file' => 'required|mimes:zip|max:1024000',
        'watch_time' => 'required|numeric|min:1',
        'view_limit_option' => 'required|string',
        'view_limit' => 'nullable|numeric|min:1',
    ]);

    Log::info(" Validation Passed");

    $zip = $request->file('zip_file');
    $folderName = 'scorm_' . time();
    $extractPath = public_path('scorm_packages/' . $folderName);

    File::makeDirectory($extractPath, 0755, true);
    $zipPath = $extractPath . '/' . $zip->getClientOriginalName();

    $zip->move($extractPath, $zip->getClientOriginalName());
    Log::info("Extract path: $extractPath");
    Log::info(" Zip moved to: $zipPath");

    $zipArchive = new ZipArchive;
    if ($zipArchive->open($zipPath)) {
        $zipArchive->extractTo($extractPath);
        $zipArchive->close();
        unlink($zipPath);
        Log::info("Zip extracted successfully");
    } else {
        Log::error("Zip could not be opened.");
        return back()->with('error', 'Zip extraction failed.');
    }

    //  Find imsmanifest.xml recursively
    $manifestPath = $this->findManifest($extractPath);
    Log::info(" Searching manifest: $manifestPath");

    if (!$manifestPath || !file_exists($manifestPath)) {
        Log::error("imsmanifest.xml not found");
        return back()->with('error', 'SCORM manifest file not found inside zip.');
    }

    //  Parse launch file
    $launchFile = null;
    $xml = simplexml_load_file($manifestPath);
    $xml->registerXPathNamespace('ns', 'http://www.imsproject.org/xsd/imscp_rootv1p1p2');
    $resource = $xml->xpath('//ns:resource')[0] ?? null;

    if ($resource) {
        $base = (string) $resource['base'] ?? '';
        $href = (string) $resource['href'];
        $launchFile = $base ? ($base . '/' . $href) : $href;
    }

    $launchFullPath = dirname($manifestPath) . '/' . $launchFile;

    if (!$launchFile || !file_exists($launchFullPath)) {
        Log::error(" Launch file does not exist: $launchFullPath");
        return back()->with('error', 'Launch file not found inside extracted folder.');
    }

    // Handle view_limit
    $viewLimit = $request->view_limit_option === 'custom'
        ? $request->view_limit
        : intval($request->view_limit_option);

    // Save to DB
    AppScormPackage::create([
        'title' => $request->title,
        'folder_name' => $folderName,
        'launch_file' => str_replace($extractPath . '/', '', $launchFullPath),
        'watch_time' => $request->watch_time,
        'view_limit' => $viewLimit,
    ]);

    Log::info(" SCORM uploaded and saved successfully.");
    return back()->with('success', 'SCORM course uploaded successfully!');
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

    // Full path for checking file existence
    $launchPath = public_path('scorm_packages/' . $package->folder_name . '/' . $package->launch_file);
    if (!file_exists($launchPath)) {
        abort(404, 'Launch file not found on server');
    }

    // Public URL for iframe
    $launchUrl = asset('scorm_packages/' . $package->folder_name . '/' . $package->launch_file);

    $userId = auth()->id();
    $progress = AppCourseProgress::where('user_id', $userId)
        ->where('course_id', $id)
        ->first();

    // ✅ Safe default values for new users (or no progress yet)
    $resumeTime = 0;
    $lastLocation = '';
    $lessonStatus = '';
    $score = '';
    $suspendData = '';

    // ✅ If progress record exists, overwrite defaults
    if ($progress) {
        $resumeTime = $progress->resume_from_time ?? 0;
        $lastLocation = $progress->cmi_core_lesson_location ?? '';
        $lessonStatus = $progress->cmi_core_lesson_status ?? '';
        $score = $progress->cmi_core_score_raw ?? '';
        $suspendData = $progress->progress_data['suspend_data'] ?? '';
    }

    return view('view', [
        'launchUrl'     => $launchUrl,
        'title'         => $package->title,
        'courseId'      => $id,
        'resumeTime'    => $resumeTime,
        'lastLocation'  => $lastLocation,
        'lessonStatus'  => $lessonStatus,
        'score'         => $score,
        'suspendData'   => $suspendData, 
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
}
