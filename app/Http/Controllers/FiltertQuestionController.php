<?php

namespace App\Http\Controllers;

use App\Subchapter;
use App\Models\Chapter;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FiltertQuestionController extends Controller
{
    

//    public function getChapterBySubject(Request $request)
// {
//     $token = session('token');

//     $response = Http::withHeaders([
//         'Authorization' => 'Bearer ' . $token
//     ])
//     ->withOptions([
//         'verify' => false  
//     ])
//     ->post('https://apexstg.datavoice.co.in/api/v1/get-chapterBy-subject', [
//         'subjects_id' => $request->subjects_id
//     ]);

//     return $response->json();
// }

 public function getChapterBySubject(Request $request)
    {
        /*"We retrieve all topics of a particular chapter. 
        In $idStatusOne, we have those topics with a status of 1. 
        In $idNoInc, we have those topics with a status of 0, 
        but only if there is at least one topic with a status of 1. 
        Therefore, if there is one topic with a status of 1 and another with a status of 0, we should not allow the creation of a subchapter, as one active topic is present."
        */
        if(isset($request->noSubjTopic)){
            $idStatusOne=[];
            $idNoInc=[];
            $id = $request->input('subjects_id');
            $idDetails = DB::table('topics')
                        ->where('topics.subject_id', $id)
                        ->whereNull('topics.subchapter_id')
                        ->select('topics.*')
                        ->get()
                        ->toArray();
            foreach($idDetails as $idDetail){
                if($idDetail->status == 1){
                    $idStatusOne[$idDetail->chapters_id][]=$idDetail->id;
                }
            }

            foreach($idDetails as $idDetail){
                foreach($idStatusOne as $key=>$value){
                    if($idDetail->chapters_id == $key){
                        $idNoInc[]=$idDetail->id;
                    }
                }
            }
            
            $chapters = Chapter::select('chapters.*')
                        ->leftJoin('topics', 'chapters.id', '=', 'topics.chapters_id')
                        ->where('chapters.status', 1)
                        ->where('chapters.subject_id', $id)
                        ->where(function ($query) use ($id) {
                            $query->where(function ($q) use ($id) {
                                $q->whereNotNull('topics.subchapter_id');
                            })
                            ->orWhere(function ($q) use ($id) {
                                $q->where('topics.status', 0);
                            })
                            ->orWhereNull('topics.chapters_id');
                        })
                        ->where(function ($query) use ($idNoInc) {
                            $query->whereNotIn('topics.id', $idNoInc)
                                ->orWhereNull('topics.id');
                        })
                        ->distinct()
                        ->get();

            return response()->json($chapters);
        }
        $id = $request->input('subjects_id');
        $chapters = Chapter::where('subject_id', $id)->where('status', Subject::ACTIVE)->get();
        return response()->json($chapters);
    }


// public function getSubChapters(Request $request)
// {
//     $token = session('token');
// //dd($token);
//     $response = Http::withHeaders([
//         'Authorization' => 'Bearer ' . $token
//     ])
//         ->withOptions([
//         'verify' => false  
    
//     ])->post('https://apexstg.datavoice.co.in/api/v1/get-subChapterBy-chapter', [
//         'chapters_id' => $request->chapter_id
//     ]);

//     return $response->json();
// }

 public function getSubChapters(Request $request){
        $id = $request->input('chapters_id');
        $sub_chapters = Subchapter::where('chapter_id', $id)->where('status', Subject::ACTIVE)->get();
        return response()->json($sub_chapters);
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
            'verify' => false   // 
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
