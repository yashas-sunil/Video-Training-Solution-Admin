<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FiltertQuestionController extends Controller
{
    

   public function getChapterBySubject(Request $request)
{
    $token = session('token');

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])
    ->withOptions([
        'verify' => false  
    ])
    ->post('https://apexstg.datavoice.co.in/api/v1/get-chapterBy-subject', [
        'subjects_id' => $request->subjects_id
    ]);

    return $response->json();
}

public function getSubChapters(Request $request)
{
    $token = session('token');
//dd($token);
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])
        ->withOptions([
        'verify' => false  
    
    ])->post('https://apexstg.datavoice.co.in/api/v1/get-subChapterBy-chapter', [
        'chapters_id' => $request->chapter_id
    ]);

    return $response->json();
}

public function filterQBundle(Request $request)
{
    $token = session('token');

    $request->validate([
        'subject_id' => 'required|integer',
        'chapter_id' => 'required|integer',
        'subchapter_id' => 'nullable|integer',
        'difficult_level_id' => 'required|integer',
        'used_status' => 'nullable|string',
        'limit' => 'nullable|integer|min:1|max:500',
    ]);

    try {
        $payload = [
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'subchapter_id' => $request->subchapter_id,
            'difficult_level_id' => $request->difficult_level_id,
            'used_status' => $request->used_status,
            'limit' => $request->limit ?? 50,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])
        ->withOptions([
            'verify' => false   // 👈 SSL verify off kar diya
        ])
        ->post('https://apexstg.datavoice.co.in/api/v1/qbundle/filter', $payload);

        return $response->json();

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Server error',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function modePage($id)
{
    return view('coursemode', ['courseId' => $id]);
}


}
