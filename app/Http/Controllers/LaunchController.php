<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ScormPackage;
use App\Assignedcourse;
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
        abort(403, 'Invalid launch request');
    }

    try {
        if (Carbon::createFromTimestamp($request->timestamp)
            ->addMinutes(10)
            ->isPast()) {
            abort(403, 'Launch link expired');
        }
    } catch (\Exception $e) {
        abort(403, 'Invalid timestamp');
    }

    $token = $request->bearerToken();
    if (!$token) {
        abort(403, 'Authorization token missing');
    }

    $user = User::where('student_uid', $request->student_id)
        ->where('email', $request->email)
        ->where('role', 2)
        ->where('api_token', $token)
        ->first();

    if (!$user) {
        abort(403, 'Invalid student or token');
    }

    $courseName = str_replace('-', ' ', $request->course_id);

    $scormCourse = ScormPackage::whereRaw(
        'LOWER(title) = ?',
        [strtolower($courseName)]
    )->first();

    if (!$scormCourse) {
        abort(404, 'SCORM course not found');
    }

    $assignedCourse = AssignedCourse::where('user_id', $user->id)
        ->where('course_id', $scormCourse->id)
        ->where('expire_date', '>=', now())
        ->first();

    if (!$assignedCourse) {
        abort(403, 'Course not assigned or expired');
    }

    Auth::login($user);

    return redirect('/view/' . $scormCourse->id);
}


}
