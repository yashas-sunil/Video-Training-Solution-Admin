<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function fetchSolutionByQuestions(Request $request)
    {
        // Minimal stub to avoid route resolution errors.
        return response()->json(['message' => 'OK', 'data' => []]);
    }

    public function fetchSolutionByQuestionsAlt(Request $request)
    {
        // Some routes reference the same method name; provide an alternate.
        return response()->json(['message' => 'OK', 'data' => []]);
    }
}
