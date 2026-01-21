<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\StudentToken;
use Illuminate\Http\Request;
use App\StudentToken as AppStudentToken;

class StudentTokenController extends Controller
{
  public function generate(Request $request)
{
    $request->validate([
        'student_id' => 'required',
        'email'      => 'required|email',
        'phone'      => 'required'
    ]);

    $user = User::where('id', $request->student_id)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Student ID not found'
        ], 404);
    }

    if ($user->phone != $request->phone) {
        return response()->json([
            'status' => false,
            'message' => 'Phone number not matched with this student id'
        ], 422);
    }

    if ($user->email != $request->email) {
        return response()->json([
            'status' => false,
            'message' => 'Email not matched with this student id'
        ], 422);
    }
    $existing = AppStudentToken::where('student_id', $request->student_id)->first();

    if ($existing) {
        return response()->json([
            'status' => true,
            'message' => 'Token already generated',
            'token' => $existing->token
        ]);
    }

    $token = Str::random(40);

    $studentToken = AppStudentToken::create([
        'student_id' => $request->student_id,
        'email'      => $request->email,
        'phone'      => $request->phone,
        'token'      => $token
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Token generated successfully',
        'student_id' => $studentToken->student_id,
        'token' => $studentToken->token
    ]);
}
}