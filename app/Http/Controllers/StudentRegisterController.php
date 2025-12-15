<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ScormPackage;
use App\Assignedcourse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentRegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string',
            'email'       => 'required|email|unique:users,email',
           'phone' => 'required|digits:10|unique:users,phone',
            'course'      => 'required|string',
            'amount_paid' => 'required|numeric',
            'expiry_date' => 'required|date|after:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $courseName = str_replace('-', ' ', $request->course);

        $scormCourse = ScormPackage::whereRaw(
            'LOWER(title) = ?',
            [strtolower($courseName)]
        )->first();

        if (!$scormCourse) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Course not found in SCORM packages'
            ], 404);
        }

        $student = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make(Str::random(10)),
            'role'     => 2
        ]);

        $studentUid = 'STD-' . str_pad($student->id, 6, '0', STR_PAD_LEFT);
        $student->student_uid = $studentUid;
        $student->save();

        AssignedCourse::create([
            'user_id'     => $student->id,
            'course_id'   => $scormCourse->id,
            'amount'      => $request->amount_paid,
            'expire_date' => $request->expiry_date,
            'status'      => 'active'
        ]);

        return response()->json([
            'status'     => 'success',
            'student_id' => $studentUid,
            'message'    => 'Student registered successfully'
        ]);
    }
}
