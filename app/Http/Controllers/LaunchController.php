<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ScormPackage;
use App\Assignedcourse;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LaunchController extends Controller
{

public function launch(Request $request)
{
    if (
        !$request->student_id ||
        !$request->course_id ||
        !$request->email ||
        !$request->timestamp
    ) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid launch request'
        ], 403);
    }

    try {
        if (
            \Carbon\Carbon::createFromTimestamp($request->timestamp)
                ->addMinutes(10)
                ->isPast()
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Launch link expired'
            ], 403);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid timestamp'
        ], 403);
    }

    $token = $request->bearerToken();
    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Authorization token missing'
        ], 403);
    }

    $user = User::where('student_uid', $request->student_id)
        ->where('email', $request->email)
        ->where('role', 2)
        ->where('api_token', $token)
        ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid student or token'
        ], 403);
    }

    $courseName = str_replace('-', ' ', $request->course_id);

    $scormCourse = ScormPackage::whereRaw(
        'LOWER(title) = ?',
        [strtolower($courseName)]
    )->first();

    if (!$scormCourse) {
        return response()->json([
            'status' => false,
            'message' => 'SCORM course not found'
        ], 404);
    }

    $assignedCourse = AssignedCourse::where('user_id', $user->id)
        ->where('course_id', $scormCourse->id)
        ->where('expire_date', '>=', now())
        ->first();

    if (!$assignedCourse) {
        return response()->json([
            'status' => false,
            'message' => 'Course not assigned or expired'
        ], 403);
    }

    $chapter = Chapter::where('course_id', $scormCourse->id)
        ->orderBy('id', 'asc')   
        ->first();

    if (!$chapter) {
        return response()->json([
            'status' => false,
            'message' => 'No chapter found for this course'
        ], 404);
    }

    Auth::login($user);

    // Example: http://127.0.0.1:8000/view/chapter/38
$viewUrl = url('/auto-login/chapter/' . $chapter->id . '?uid=' . $user->id . '&token=' . $token);

    return response()->json([
        'status'    => true,
        'message'   => 'Launch successful',
        // 'course_id' => $scormCourse->id,
        // 'chapter_id'=> $chapter->id,
        'view_url'  => $viewUrl
    ]);
}


}
