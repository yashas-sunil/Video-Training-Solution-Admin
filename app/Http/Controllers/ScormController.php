<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use App\CourseProgress;
use App\ScormPackage as AppScormPackage;
use ZipArchive;
use Illuminate\Http\Request;
use App\CourseProgress as AppCourseProgress;

class ScormController extends Controller
{
     public function showForm()
    {
        return view('upload');
    }

public function upload(Request $request)
{
    try {
        Log::info('🚀 SCORM Upload Start');

        $request->validate([
            'title' => 'required|string',
            'zip_file' => 'required|mimes:zip|max:1024000',
        ]);
        Log::info('✅ Validation Passed');

        $zip = $request->file('zip_file');
        Log::info('📁 File received: ' . $zip->getClientOriginalName());

        $folderName = 'scorm_' . time();
        $extractPath = public_path('scorm_packages/' . $folderName);
        Log::info('📁 Extract path: ' . $extractPath);

        if (!mkdir($extractPath, 0775, true)) {
            Log::error('❌ Could not create folder: ' . $extractPath);
            return back()->with('error', 'Could not create folder for SCORM.');
        }

        $zipPath = $extractPath . '/' . $zip->getClientOriginalName();
        $zip->move($extractPath, $zip->getClientOriginalName());
        Log::info('📦 Zip moved to: ' . $zipPath);

        $zipArchive = new ZipArchive;
        if ($zipArchive->open($zipPath)) {
            $zipArchive->extractTo($extractPath);
            $zipArchive->close();
            Log::info('✅ Zip extracted successfully');
            unlink($zipPath);
        } else {
            Log::error('❌ Failed to open zip file');
            return back()->with('error', 'Failed to open zip file');
        }

        $manifestPath = $extractPath . '/imsmanifest.xml';
        Log::info('🔍 Checking manifest at: ' . $manifestPath);

        $launchFile = null;
        if (file_exists($manifestPath)) {
            $xml = simplexml_load_file($manifestPath);
            $xml->registerXPathNamespace('ns', 'http://www.imsproject.org/xsd/imscp_rootv1p1p2');
            $resource = $xml->xpath('//ns:resource')[0] ?? null;

            if ($resource) {
                $base = (string) $resource['base'] ?? '';
                $href = (string) $resource['href'];
                $launchFile = $base ? ($base . '/' . $href) : $href;
                Log::info('🎯 Launch file found: ' . $launchFile);
            } else {
                Log::warning('⚠️ No resource found in manifest');
            }
        } else {
            Log::error('❌ imsmanifest.xml not found');
        }

        if (!$launchFile || !file_exists($extractPath . '/' . $launchFile)) {
            Log::error('❌ Launch file does not exist: ' . $extractPath . '/' . $launchFile);
            return back()->with('error', 'Launch file not found.');
        }

        AppScormPackage::create([
            'title' => $request->title,
            'folder_name' => $folderName,
            'launch_file' => $launchFile,
        ]);

        Log::info('✅ SCORM saved in DB successfully');
        return back()->with('success', 'SCORM course uploaded successfully.');
    } catch (\Throwable $e) {
        Log::error('🔥 Exception: ' . $e->getMessage());
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

public function view($id)
{
    $package = AppScormPackage::findOrFail($id);

    $launchPath = public_path('scorm_packages/' . $package->folder_name . '/' . $package->launch_file);
    if (!file_exists($launchPath)) {
        abort(404, 'Launch file not found on server');
    }

    $launchUrl = asset('scorm_packages/' . $package->folder_name . '/' . $package->launch_file);

    $userId = auth()->id();
    $progress = AppCourseProgress::where('user_id', $userId)
                ->where('course_id', $id)
                ->first();

    return view('view', [
        'launchUrl' => $launchUrl,
        'title' => $package->title,
        'courseId' => $id,
        'lastLocation' => optional($progress)->cmi_core_lesson_location,
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

}
