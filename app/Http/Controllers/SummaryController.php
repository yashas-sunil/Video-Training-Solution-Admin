<?php
namespace App\Http\Controllers;

use App\Summary;
use Carbon\Carbon;
use App\Subchapter;
use App\Models\Topic;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Content;
use App\Models\MailLog;
use App\Models\Subject;
use App\Models\UsersTopic;
use App\Models\UsersChapter;
use Illuminate\Http\Request;
use App\Models\UserSubChapter;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $view_details = $this->viewSummary();
        $courses      = $this->getCourses();
        return view('summary', compact('view_details', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getSubjectbyCourses()
    {

        /*$subject = Subject::where('course_id', request('course_id'))
            ->where('status',Subject::ACTIVE)
            ->orderBy('name', 'asc') 
            ->get()
            ->toArray();*/

        $subject = Subject::select('subjects.id', 'subjects.name')
            ->join('chapters', 'subjects.id', '=', 'chapters.subjects_id')
            ->join('summeries', 'chapters.id', '=', 'summeries.chapters_id')
            ->whereNull('summeries.content')
            ->where('subjects.status', Subject::ACTIVE)
            ->where('subjects.course_id', request('course_id'))
            ->distinct()
            ->orderBy('subjects.name')
            ->get()->toArray();

        return $subject;
    }
    public function getChapterbySubject()
    {
        $chapter = Chapter::select('chapters.id', 'chapters.name')
            ->join('summeries', 'chapters.id', '=', 'summeries.chapters_id')
            ->where('summeries.content', null)
            ->where('chapters.status', Chapter::ACTIVE)
            ->where('chapters.subjects_id', request('subjects_id'))
            ->orderBy('chapters.NAME')
            ->get()
            ->toArray();

        return $chapter;
    }

    public function viewSummary()
    {

        $details = Summary::with('course', 'level', 'subject', 'chapters')->where('status', 1)->get();
        return $details;
    }

    public function getSummary()
    {
        $summaryContent = Summary::where('id', request('chapter_id'))->value('content');

        return response()->json(['data' => $summaryContent]);
    }

    public function addSummary(Request $Request)
    {
        try
        {
            if ($Request->has('unfiltered-summary-flag')) {
                $validatedData = $Request->validate([
                    'unfiltered-file-input' => 'required',
                    'unfiltered-course'     => 'required',
                    'unfiltered-level'      => 'required',
                    'unfiltered-subject'    => 'required',
                    'unfiltered-chapter'    => 'required',
                ]);

                $request                            = new Request();
                $request['fileInput']               = $Request['unfiltered-file-input'];
                $request['course']                  = $Request['unfiltered-course'];
                $request['level']                   = $Request['unfiltered-level'];
                $request['subject']                 = $Request['unfiltered-subject'];
                $request['chapter']                 = $Request['unfiltered-chapter'];
                $request['unfiltered-summary-flag'] = $Request['unfiltered-summary-flag'];
            } else {
                $validatedData = $Request->validate([
                    'fileInput'  => 'required',
                    'course'     => 'required',
                    'level'      => 'required',
                    'subject'    => 'required',
                    'chapter'    => 'required',
                    'ImageInput' => 'nullable',
                ]);
                $request = new Request($validatedData);
            }
            $chapter_name = Chapter::where('id', intval($request->chapter))
                ->pluck('name')
                ->first();

            $has_subchapter                  = null;
            $extra_content_error             = [];
            $difference                      = [];
            $has_error                       = 0;
            $topic_not_present               = [];
            $sub_chapter_topics_missing_name = [];

            $file         = $request['fileInput'];
            $originalName = $file->getClientOriginalName();
            // $originalName = dechex(time()) . '_' . str_replace(' ', '_', $originalName);
            $originalName  = str_replace(' ', '_', $originalName);
            $saveName      = explode(".", $originalName);
            $nameToSave    = $saveName[0] . ".html";
            $relativePath  = Storage::putFileAs('public/summaries/' . str_replace(' ', '_', $chapter_name), $file, $originalName);
            $absolutePath1 = storage_path('app/' . $relativePath);
            $absolutePath2 = storage_path('app/public/summaries/' . str_replace(' ', '_', $chapter_name) . '/' . $nameToSave);
            $fileContent   = file_get_contents($file->getRealPath());
            $pandocPath    = env("PANDOC_PATH");
            $process       = new Process([
                $pandocPath,
                $absolutePath1,
                '-t',
                'html',
                '-o',
                $absolutePath2,
            ]);

            $process->mustRun();
            $output = $process->getOutput();

            $has_subchapter = Subchapter::where('chapter_id', $request->chapter)
                ->where('status', 1)
                ->get()
                ->toArray();
            $fileContentHtml = file_get_contents($absolutePath2);

            if (count($has_subchapter) > 0) { // subchapter present

                /*Check if Sub-chapters are present*/

                $pattern_subchapters = '/<h1[^>]*id="([^"]*)"[^>]*>(.*?)<\/h1>(.*?)(?=<h1|$)/s';

                preg_match_all($pattern_subchapters, $fileContentHtml, $headings, PREG_SET_ORDER);

                $heading_1 = array_map(function ($headingItem) {
                    return strtolower(trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n", ' '], '', $headingItem[2]))));
                }, $headings);

                $sub_chapter = array_map(function ($topicItem) {
                    return strtolower(str_replace(' ', '', $topicItem['name']));
                }, $has_subchapter);

                $subchapter_diff = array_diff($sub_chapter, $heading_1);

                if (count($heading_1) > count($sub_chapter) && empty($subchapter_diff)) { // If subchapter other than that linked to chapter are present in summary file

                    $extra_sub_chapter = array_diff($heading_1, $sub_chapter);

                    foreach ($extra_sub_chapter as $index => $extra_chapter) {
                        $extra_content_error[$chapter_name][] = $headings[$index][2];
                    }

                    $content                   = json_encode($extra_content_error);
                    $insert_mail               = new MailLog();
                    $insert_mail->to_email     = "bhagwan@datavoice.co.in";
                    $insert_mail->subject      = "Wrong Summary Upload for Chapter " . $chapter_name;
                    $insert_mail->mail_content = $content;
                    $insert_mail->blade_name   = "additional_subchapter_error";
                    $insert_mail->email_type   = "wrong_summary";
                    $insert_mail->status       = 0;
                    $insert_mail->attachments  = $originalName;
                    $insert_mail->save();

                    if (! (isset($Request['unfiltered-summary-flag']))) {
                        $wrongFilePath = Storage::putFileAs('public/wrong_summary', $file, $originalName);
                        unlink($absolutePath1);
                        unlink($absolutePath2);

                        return redirect()->route('add-summary')->with('error', 'Summary failed to upload');
                    }
                }

                if (count($subchapter_diff) > 0) { //If sub-chapter name is incorrect

                    foreach ($subchapter_diff as $key => $diff) {
                        if (! isset($difference[$chapter_name])) {
                            $difference[$chapter_name] = [];
                        }
                        $difference[$chapter_name][] = $has_subchapter[$key];
                    }
                    $difference['type'] = "sub_chapter";
                }

                if (isset($Request['unfiltered-summary-flag']) && count($difference) > 0) {
                    $content                   = json_encode($difference);
                    $insert_mail               = new MailLog();
                    $insert_mail->to_email     = "bhagwan@datavoice.co.in";
                    $insert_mail->subject      = "Wrong Summary Upload for Chapter " . $chapter_name;
                    $insert_mail->mail_content = $content;
                    $insert_mail->blade_name   = "summary_wrong";
                    $insert_mail->email_type   = "wrong_summary";
                    $insert_mail->status       = 0;
                    $insert_mail->attachments  = $originalName;
                    $insert_mail->save();

                    for ($iCnt = 0; $iCnt < count($difference[$chapter_name]); $iCnt++) {
                        $sub_chapter_topics_missing = Topic::where('chapters_id', $request->chapter)
                            ->where('subchapter_id', $difference[$chapter_name][$iCnt]['id']) // Ensure this ID is set correctly
                            ->where('status', 1)
                            ->select('name', 'id')
                            ->get()
                            ->toArray();

                        $sub_chapter_topics_missing_ids  = array_column($sub_chapter_topics_missing, 'id');
                        $sub_chapter_topics_missing_name = array_column($sub_chapter_topics_missing, 'name');
                        foreach ($sub_chapter_topics_missing_ids as $sub_chapter_topic_missing) {
                            $content = Content::where('topics_id', $sub_chapter_topic_missing)->first();
                            if ($content) {
                                $content->status = 0;
                                $content->save();
                            }
                        }
                    }
                }

                if (count($difference) == 0 || isset($Request['unfiltered-summary-flag'])) {

                    $difference = [];

                    for ($i = 0; $i < count($headings); $i++) {
                        $chk_topic     = $headings[$i];
                        $pattern_topic = '/<h2[^>]*id="([^"]*)"[^>]*>(.*?)<\/h2>/s';

                        preg_match_all($pattern_topic, $chk_topic[3], $topic_headings, PREG_SET_ORDER);

                        $heading_2 = array_map(function ($headingItem) {
                            return strtolower(trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n", ' '], '', $headingItem[2]))));
                        }, $topic_headings);

                        $sub_chapter_topic = Topic::where('chapters_id', $request->chapter)
                            ->where('subchapter_id', $has_subchapter[$i]['id'])
                            ->where('status', 1)
                            ->pluck('name')
                            ->toArray();

                        $sub_chapter_topic_cleaned = array_map(function ($topicItem) {
                            return strtolower(str_replace(' ', '', $topicItem));
                        }, $sub_chapter_topic);

                        $topic_diff = array_diff($sub_chapter_topic_cleaned, $heading_2);

                        if (count($topic_diff) > 0) { //If topic name for sub-chapter is incorrect
                            foreach ($topic_diff as $key => $diff) {
                                $subchapterName = $has_subchapter[$i]['name'];
                                if (! isset($difference[$chapter_name][$subchapterName])) {
                                    $difference[$chapter_name][$subchapterName] = [];
                                }
                                $difference[$chapter_name][$subchapterName][] = $sub_chapter_topic[$key];
                            }
                            $topic_not_present[] = $sub_chapter_topic[$key];
                        }

                        if (count($heading_2) > count($sub_chapter_topic_cleaned) && empty($topic_diff)) { //If topic inside sub-chapter other than that linked to sub-chapter present in summary file
                            $has_error               = 1;
                            $extra_sub_chapter_topic = array_diff($heading_2, $sub_chapter_topic_cleaned);

                            foreach ($extra_sub_chapter_topic as $index => $extra_sub_topic) {
                                $subchapterName = $has_subchapter[$i]['name'];
                                if (! isset($extra_content_error[$chapter_name][$subchapterName])) {
                                    $extra_content_error[$chapter_name][$subchapterName] = [];
                                }
                                $extra_content_error[$chapter_name][$subchapterName][] = $topic_headings[$index][2];
                            }
                        } else if (isset($Request['unfiltered-summary-flag'])) {
                            $has_error               = 1;
                            $extra_sub_chapter_topic = array_diff($heading_2, $sub_chapter_topic_cleaned);

                            foreach ($extra_sub_chapter_topic as $index => $extra_sub_topic) {
                                $subchapterName = $has_subchapter[$i]['name'];
                                if (! isset($extra_content_error[$chapter_name][$subchapterName])) {
                                    $extra_content_error[$chapter_name][$subchapterName] = [];
                                }
                                $extra_content_error[$chapter_name][$subchapterName][] = $topic_headings[$index][2];
                            }
                        }
                    }

                    if ($has_error == 1) {

                        if (count($extra_content_error) > 0) {
                            $content                   = json_encode($extra_content_error);
                            $insert_mail               = new MailLog();
                            $insert_mail->to_email     = "bhagwan@datavoice.co.in";
                            $insert_mail->subject      = "Wrong Summary Upload for Chapter " . $chapter_name;
                            $insert_mail->mail_content = $content;
                            $insert_mail->blade_name   = "additional_subchapter_topic_error";
                            $insert_mail->email_type   = "wrong_summary";
                            $insert_mail->status       = 0;
                            $insert_mail->attachments  = $originalName;
                            $insert_mail->save();

                            if (! (isset($Request['unfiltered-summary-flag']))) {

                                $wrongFilePath = Storage::putFileAs('public/wrong_summary', $file, $originalName);
                                unlink($absolutePath1);
                                unlink($absolutePath2);

                                return redirect()->route('add-summary')->with('error', 'Summary failed to upload');
                            }
                        }
                    }
                }
            } else { // no subchapters present

                // check if any sub-chapter is purposely added

                $pattern = '/<h1[^>]*id="([^"]*)"[^>]*>(.*?)<\/h1>/s';
                preg_match_all($pattern, $fileContentHtml, $h3_present, PREG_SET_ORDER);

                if ($h3_present) {
                    $wrongFilePath = Storage::putFileAs('public/wrong_summary', $file, $originalName);
                    unlink($absolutePath1);
                    unlink($absolutePath2);

                    return redirect()->route('add-summary')->with('error', 'Following Chapter does not have Sub Chapter please remove and re-upload');
                }

                //To check if all topic's of summary in database is present in word doc
                $pattern = '/<h2[^>]*id="([^"]*)"[^>]*>(.*?)<\/h2>/s';
                preg_match_all($pattern, $fileContentHtml, $headings, PREG_SET_ORDER);

                $heading = array_map(function ($headingItem) {
                    return strtolower(trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n", ' '], '', $headingItem[2]))));
                }, $headings);

                $topics = Topic::where('chapters_id', $request->chapter)
                    ->where('status', 1)
                    ->pluck('name')
                    ->toArray();

                $topic = array_map(function ($topicItem) {
                    return strtolower(str_replace(' ', '', $topicItem));
                }, $topics);

                $no_sub_diff = array_diff($topic, $heading);

                if (count($heading) > count($topic) && empty($no_sub_diff)) { // If topics other than that linked to chapter are present in summary file

                    $extra_topic = array_diff($heading, $topic);

                    foreach ($extra_topic as $index => $ex_topic) {
                        $extra_content_error[$chapter_name][] = $headings[$index][2];
                    }

                    $content                   = json_encode($extra_content_error);
                    $insert_mail               = new MailLog();
                    $insert_mail->to_email     = "bhagwan@datavoice.co.in";
                    $insert_mail->subject      = "Wrong Summary Upload for Chapter " . $chapter_name;
                    $insert_mail->mail_content = $content;
                    $insert_mail->blade_name   = "additional_topic_error";
                    $insert_mail->email_type   = "wrong_summary";
                    $insert_mail->status       = 0;
                    $insert_mail->attachments  = $originalName;
                    $insert_mail->save();

                    if (! (isset($Request['unfiltered-summary-flag']))) {

                        $wrongFilePath = Storage::putFileAs('public/wrong_summary', $file, $originalName);
                        unlink($absolutePath1);
                        unlink($absolutePath2);

                        return redirect()->route('add-summary')->with('error', 'Summary failed to upload');
                    }
                } elseif (isset($Request['unfiltered-summary-flag'])) {
                    $extra_topic = array_diff($heading, $topic);

                    foreach ($extra_topic as $index => $ex_topic) {
                        $extra_content_error[$chapter_name][] = $headings[$index][2];
                    }

                    if (count($extra_content_error) > 0) {
                        $content                   = json_encode($extra_content_error);
                        $insert_mail               = new MailLog();
                        $insert_mail->to_email     = "bhagwan@datavoice.co.in";
                        $insert_mail->subject      = "Wrong Summary Upload for Chapter " . $chapter_name;
                        $insert_mail->mail_content = $content;
                        $insert_mail->blade_name   = "additional_topic_error";
                        $insert_mail->email_type   = "wrong_summary";
                        $insert_mail->status       = 0;
                        $insert_mail->attachments  = $originalName;
                        $insert_mail->save();
                    }
                }

                if (count($no_sub_diff) > 0) { //If not sub-chapter present and topic name of chapter is incorrect

                    foreach ($no_sub_diff as $key => $diff) {

                        if (! isset($difference[$chapter_name])) {
                            $difference[$chapter_name] = [];
                        }
                        $difference[$chapter_name][] = $topics[$key];
                    }
                    $difference['type']  = "chapter_topic";
                    $topic_not_present[] = $topics[$key];
                }
            }

            if (count($difference) > 0 && ! (isset($Request['unfiltered-summary-flag']))) { //Any error while uploading

                $content                   = json_encode($difference);
                $insert_mail               = new MailLog();
                $insert_mail->to_email     = "bhagwan@datavoice.co.in";
                $insert_mail->subject      = "Wrong Summary Upload for Chapter " . $chapter_name;
                $insert_mail->mail_content = $content;
                $insert_mail->blade_name   = "summary_wrong";
                $insert_mail->email_type   = "wrong_summary";
                $insert_mail->status       = 0;
                $insert_mail->attachments  = $originalName;
                $insert_mail->save();

                if (! (isset($Request['unfiltered-summary-flag']))) {

                    $wrongFilePath = Storage::putFileAs('public/wrong_summary', $file, $originalName);
                    unlink($absolutePath1);
                    unlink($absolutePath2);

                    return redirect()->route('add-summary')->with('error', 'Summary failed to upload');
                }
            } else { //All sub-chapters (if exist), topics present

                if (count($difference)) {
                    $content                   = json_encode($difference);
                    $insert_mail               = new MailLog();
                    $insert_mail->to_email     = "bhagwan@datavoice.co.in";
                    $insert_mail->subject      = "Wrong Summary Upload for Chapter " . $chapter_name;
                    $insert_mail->mail_content = $content;
                    $insert_mail->blade_name   = "summary_wrong";
                    $insert_mail->email_type   = "wrong_summary";
                    $insert_mail->status       = 0;
                    $insert_mail->attachments  = $originalName;
                    $insert_mail->save();
                }

                $imageArray = [];

                /*conversion of table to image*/
                $fileContentHtml = Summary::tableTOImage($fileContentHtml, $chapter_name);
                /**/

                if ($request['ImageInput']) {
                    foreach ($request['ImageInput'] as $image) {
                        $imageName    = $image->getClientOriginalName();
                        $imageName    = dechex(time()) . '_' . str_replace(' ', '_', $imageName);
                        $path         = Storage::putFileAs('public/summaries/' . str_replace(' ', '_', $chapter_name), $image, $imageName);
                        $imageArray[] = $imageName;
                    }
                }

                /*store all image name into database*/
                $fileContentHtml = Summary::appendImage($fileContentHtml, $imageArray, $chapter_name);
                file_put_contents($absolutePath2, $fileContentHtml);
                /**/

                /*conversion of flash card to audio*/
                Summary::flashAudioConversion($fileContentHtml, $chapter_name);
                /**/

                /*merge all audio file for summary*/
                $summary_audio = Summary::mergeAudioFile($chapter_name, $request->chapter);
                /**/

                $imageString     = implode(', ', $imageArray);
                $existingSummary = Summary::where('chapters_id', $request->chapter)->first();
                if ($existingSummary) {
                    Summary::where('chapters_id', $request->chapter)
                        ->update([
                            'name'            => $nameToSave,
                            'summeries_image' => $imageString,
                            'content_audio'   => $summary_audio,
                        ]);
                } else {
                    $add_summary                  = new Summary;
                    $add_summary->name            = $nameToSave;
                    $add_summary->chapters_id     = $request->chapter;
                    $add_summary->course_id       = $request->course;
                    $add_summary->content_audio   = $summary_audio;
                    $add_summary->level_id        = $request->level;
                    $add_summary->subject_id      = $request->subject;
                    $add_summary->summeries_image = $imageString;
                    $add_summary->save();
                }

                $topic_not_present = array_merge($topic_not_present, $sub_chapter_topics_missing_name);

                $topic_ids = Topic::where('chapters_id', $request->chapter)
                    ->whereNotIn('name', $topic_not_present)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();

                $topics_trigger_status = Topic::where('chapters_id', $request->chapter)
                    ->whereIn('name', $topic_not_present)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();

                foreach ($topics_trigger_status as $topic_trigger_status) {
                    $content = Content::where('topics_id', $topic_trigger_status)->first();
                    if ($content) {
                        $content->status = 0;
                        $content->save();
                    }
                }

                $nameToSave = str_replace(',', '_', $nameToSave);
                foreach ($topic_ids as $topic_id) {
                    $content = Content::where('topics_id', $topic_id)->first();

                    if ($content) {
                        $content->name   = $nameToSave;
                        $content->status = 1;
                        $content->save();
                    } else {
                        Content::create([
                            'topics_id'  => $topic_id,
                            'name'       => $nameToSave,
                            'course_id'  => $request->course,
                            'level_id'   => $request->level,
                            'subject_id' => $request->subject,
                            'chapter_id' => $request->chapter,
                            'status'     => 1,
                        ]);
                    }
                }

                $response = UsersTopic::updateCompleteStatus($request);

                return redirect()->route('add-summary')->with('success', 'Summary created successfully');
            }
        } catch (ProcessFailedException $exception) {
            return $exception->getMessage();
        }
    }

    public function getCourses()
    {

        $response = Course::where('status', 1)->get();

        return $response;
    }

    public function viewHtml($chapters_id)
    {
        try {
            $chapters_id  = Crypt::decryptString($chapters_id);
            $data         = "<p><strong>Summary Not Found</strong></p>";
            $summary      = Summary::with('chapters')->where('status', Summary::ACTIVE)->where('chapters_id', $chapters_id)->first();
            $chapter_name = str_replace(' ', '_', $summary->chapters->name);
            if (! empty($summary->id)) {
                $path = storage_path('app/public/summaries/' . $chapter_name . '/' . $summary->name);
                if (file_exists($path)) {
                    $fileContent = file_get_contents($path);
                    $newContent  = $fileContent;
                    $data        = $newContent;
                } else {
                    return view('html_summary', compact('data'));
                }
                return view('html_summary', compact('data'));
            } else {
                return view('html_summary', compact('data'));
            }
        } catch (\Throwable $th) {
            return view('html_summary', compact('data'));
        }
    }
    public function chapterComplete(Request $request)
    {
        try {
            $this->validate($request, [
                'chapter_id' => 'required',
            ]);

            $sub_chapter_check = \App\Subchapter::where('chapter_id', $request->chapter_id)->first();
            if (! empty($sub_chapter_check)) {
                $sub_chapter_id = \App\Subchapter::where('chapter_id', $request->chapter_id)->where('status', \App\Subchapter::ACTIVE)->pluck('id')->toArray();
                $topic_id       = \App\Topic::where('chapters_id', $request->chapter_id)->where('status', \App\Topic::ACTIVE)->pluck('id')->toArray();
                $user_chapter   = \App\UsersChapter::where('users_id', $request->user()->id)->where('chapters_id', $request->chapter_id)->first();
                if (! empty($user_chapter)) {
                    $user_chapter->status = UsersChapter::COMPLETED;
                    $user_chapter->save();
                } else {
                    $user_chapter              = new UsersChapter;
                    $user_chapter->users_id    = $request->user()->id;
                    $user_chapter->chapters_id = $request->chapter_id;
                    $user_chapter->status      = UsersChapter::COMPLETED;
                    $user_chapter->created_at  = now();
                    $user_chapter->updated_at  = now();
                    $user_chapter->save();
                }

                foreach ($sub_chapter_id as $sub_chapter) {
                    $user_sub_chapter = UserSubChapter::where('users_id', $request->user()->id)->where('chapters_id', $request->chapter_id)->where('subchapters_id', $sub_chapter)->first();
                    if (! empty($user_sub_chapter)) {
                        $user_sub_chapter->status = UserSubChapter::COMPLETED;
                        $user_sub_chapter->save();
                    } else {
                        $user_sub_chapter                 = new UserSubChapter;
                        $user_sub_chapter->users_id       = $request->user()->id;
                        $user_sub_chapter->chapters_id    = $request->chapter_id;
                        $user_sub_chapter->subchapters_id = $sub_chapter;
                        $user_sub_chapter->status         = UserSubChapter::COMPLETED;
                        $user_sub_chapter->created_at     = now();
                        $user_sub_chapter->updated_at     = now();
                        $user_sub_chapter->save();
                    }
                }

                foreach ($topic_id as $topic) {
                    $user_topic = UsersTopic::where('users_id', $request->user()->id)->where('topics_id', $topic)->first();
                    if (! empty($user_topic)) {
                        $user_topic->status = UsersTopic::COMPLETED;
                        $user_topic->save();
                    } else {
                        $user_topic             = new UsersTopic;
                        $user_topic->users_id   = $request->user()->id;
                        $user_topic->topics_id  = $topic;
                        $user_topic->status     = UsersTopic::COMPLETED;
                        $user_topic->created_at = now();
                        $user_topic->updated_at = now();
                        $user_topic->save();
                    }
                }

                $response = [
                    'status'  => 'success',
                    'message' => 'Chapter completed successfully',
                ];
                return response()->json($response);
            } else {
                $topic_id     = Topic::where('chapters_id', $request->chapter_id)->where('status', Topic::ACTIVE)->pluck('id')->toArray();
                $user_chapter = UsersChapter::where('users_id', $request->user()->id)->where('chapters_id', $request->chapter_id)->first();
                if (! empty($user_chapter)) {
                    $user_chapter->status = UsersChapter::COMPLETED;
                    $user_chapter->save();
                } else {
                    $user_chapter              = new UsersChapter;
                    $user_chapter->users_id    = $request->user()->id;
                    $user_chapter->chapters_id = $request->chapter_id;
                    $user_chapter->status      = UsersChapter::COMPLETED;
                    $user_chapter->created_at  = now();
                    $user_chapter->updated_at  = now();
                    $user_chapter->save();
                }

                foreach ($topic_id as $topic) {
                    $user_topic = UsersTopic::where('users_id', $request->user()->id)->where('topics_id', $topic)->first();
                    if (! empty($user_topic)) {
                        $user_topic->status = UsersTopic::COMPLETED;
                        $user_topic->save();
                    } else {
                        $user_topic             = new UsersTopic;
                        $user_topic->users_id   = $request->user()->id;
                        $user_topic->topics_id  = $topic;
                        $user_topic->status     = UsersTopic::COMPLETED;
                        $user_topic->created_at = now();
                        $user_topic->updated_at = now();
                        $user_topic->save();
                    }
                }

                $response = [
                    'status'  => 'success',
                    'message' => 'Chapter completed successfully',
                ];
                return response()->json($response);
            }
        } catch (\Throwable $th) {
            $response = [
                'status'  => 'error',
                'message' => $th->getMessage(),
            ];
            return response()->json($response, 403);
        }
    }

    public function convertToMarathiNumerals($value, $devanagari)
    {
        return preg_replace_callback('/\d/', function ($matches) use ($devanagari) {
            return $devanagari[$matches[0]];
        }, $value);
    }

    public function return_vvmt_url($from_id, $to_id,$from_date,$to_date)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $fromDate = Carbon::parse($from_date)->format('Y-m-d');
        $toDate   = Carbon::parse($to_date)->format('Y-m-d');

        $devanagari = [
            0 => '०',
            1 => '१',
            2 => '२',
            3 => '३',
            4 => '४',
            5 => '५',
            6 => '६',
            7 => '७',
            8 => '८',
            9 => '९',
        ];

        $full_price = [
            0  => 0,
            5  => 10,
            8  => 15,
            10 => 20,
            13 => 25,
            15 => 30,
            18 => 35,
            20 => 40,
            23 => 45,
        ];

        $data = DB::connection('mysql_vvmt')->table('tbl_trip_ticket_details')
            ->select(
                DB::raw("'सातिवली' as fldv_depot_name"),
                'tbl_trip_ticket_details.id as id',
                'tbl_trip_ticket_details.fldv_ticket_no as ticket_no',
                'tbl_trip_ticket_details.fldv_etm_number as etm_number',
                'tbl_trip_ticket_details.flddt_ticket_date as ticket_date',
                'tbl_trip_ticket_details.fldv_ticket_time as ticket_time',
                'tbl_trip_ticket_details.fldv_waybill_no as waybill_no',
                'tbl_trip_ticket_details.fldi_half_ticket as half_ticket',
                'tbl_trip_ticket_details.fldi_full_ticket as full_ticket',
                'tbl_trip_ticket_details.fldv_conductor_employee_code as conductor_employee_code',
                'tbl_trip_ticket_details.fldi_route_no as route_no',
                'tbl_trip_ticket_details.fldi_ticket_fare as ticket_fare',
                'fromstage.fldv_stage_name_marathi as from_stage_name',
                'tostage.fldv_stage_name_marathi as to_stage_name'
            )
            ->join('tbl_waybill_mst', 'tbl_trip_ticket_details.fldv_waybill_no', '=', 'tbl_waybill_mst.fldv_waybill_no')
            ->leftJoin('tbl_route_stage_details as fromstage', function ($join) {
                $join->on('tbl_trip_ticket_details.fldi_stage_from', '=', 'fromstage.fldi_stage_no')
                    ->on('tbl_trip_ticket_details.fldi_route_no', '=', 'fromstage.fldi_route_no');
            })
            ->leftJoin('tbl_route_stage_details as tostage', function ($join) {
                $join->on('tbl_trip_ticket_details.fldi_stage_to', '=', 'tostage.fldi_stage_no')
                    ->on('tbl_trip_ticket_details.fldi_route_no', '=', 'tostage.fldi_route_no');
            })
            ->where('tbl_trip_ticket_details.id', '>=', $from_id)
            ->where('tbl_trip_ticket_details.id', '<=', $to_id)
            ->whereBetween('tbl_waybill_mst.flddt_date', [$fromDate, $toDate])
            ->where('tbl_trip_ticket_details.fldv_concession_case_code', '05')
            ->distinct()
            ->get();

        $convert_data = $data->map(function ($item) use ($devanagari, $full_price) {
            $item->ticket_fare             = number_format($item->ticket_fare / 100);
            $item->ticket_fare_display_sub = $full_price[$item->ticket_fare];
            $item->ticket_fare_display     = $full_price[$item->ticket_fare] - 0.15;

            $item->ticket_fare             = number_format($item->ticket_fare, 2);
            $item->ticket_fare_display_sub = number_format($item->ticket_fare_display_sub, 2);
            foreach ($item as $key => $value) {
                if (is_numeric($value) || preg_match('/\d/', $value)) {
                    $item->$key = $this->convertToMarathiNumerals((string) $value, $devanagari);
                }
            }
            return $item;
        });

        $iCnt         = 0;
        $counter      = 0;
        $grouped_data = [];

        foreach ($convert_data as $value) {
            if (! isset($grouped_data[$iCnt])) {
                $grouped_data[$iCnt] = [];
            }

            $grouped_data[$iCnt][] = $value;
            $counter++;

            if ($counter % 9 == 0) {
                $iCnt++;
            }
        }
        return view('demo', compact('grouped_data'));
    }

    public function vvmt_dummy(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $fromDate = Carbon::parse($request->from)->format('Y-m-d');
        $toDate   = Carbon::parse($request->to)->format('Y-m-d');

        $data = DB::connection('mysql_vvmt')->table('rpstagedetails')
            ->where('fldc_status', 'Y')
            ->first();

        $dummy_array = [];
        $data        = DB::connection('mysql_vvmt')->table('tbl_trip_ticket_details')
            ->select(
                DB::raw("'सातिवली' as fldv_depot_name"),
                'tbl_trip_ticket_details.id as id',
                'tbl_trip_ticket_details.fldv_ticket_no as ticket_no',
                'tbl_trip_ticket_details.fldv_etm_number as etm_number',
                'tbl_trip_ticket_details.flddt_ticket_date as ticket_date',
                'tbl_trip_ticket_details.fldv_ticket_time as ticket_time',
                'tbl_trip_ticket_details.fldv_waybill_no as waybill_no',
                'tbl_trip_ticket_details.fldi_half_ticket as half_ticket',
                'tbl_trip_ticket_details.fldi_full_ticket as full_ticket',
                'tbl_trip_ticket_details.fldv_conductor_employee_code as conductor_employee_code',
                'tbl_trip_ticket_details.fldi_route_no as route_no',
                'tbl_trip_ticket_details.fldi_ticket_fare as ticket_fare',
                'fromstage.fldv_stage_name_marathi as from_stage_name',
                'tostage.fldv_stage_name_marathi as to_stage_name'
            )
            ->join('tbl_waybill_mst', 'tbl_trip_ticket_details.fldv_waybill_no', '=', 'tbl_waybill_mst.fldv_waybill_no')
            ->leftJoin('tbl_route_stage_details as fromstage', function ($join) {
                $join->on('tbl_trip_ticket_details.fldi_stage_from', '=', 'fromstage.fldi_stage_no')
                    ->on('tbl_trip_ticket_details.fldi_route_no', '=', 'fromstage.fldi_route_no');
            })
            ->leftJoin('tbl_route_stage_details as tostage', function ($join) {
                $join->on('tbl_trip_ticket_details.fldi_stage_to', '=', 'tostage.fldi_stage_no')
                    ->on('tbl_trip_ticket_details.fldi_route_no', '=', 'tostage.fldi_route_no');
            })
            ->whereBetween('tbl_waybill_mst.flddt_date', [$fromDate, $toDate])
            ->where('tbl_trip_ticket_details.fldv_concession_case_code', '05')
            ->orderBy('id', 'asc')
            ->get();

        $dummy_array = [];
        $lastIndex   = count($data) - 1;
        $appUrl      = config('app.url');

        foreach ($data as $index => $item) {
            if ($index % 500 === 0) {
                $currentGroup = ['start' => $item->id];
            }

            if (($index + 1) % 500 === 0 || $index === $lastIndex) {
                $currentGroup['end'] = $item->id;

                $fromId = $currentGroup['start'];
                $toId   = $currentGroup['end'];

                $currentGroup['link'] = "{$appUrl}/vvmt/{$fromId}/{$toId}/{$fromDate}/{$toDate}";

                $dummy_array[] = $currentGroup;
            }
        }

        $htmlBody = "<h3>VVMT Ticket Links - " . Carbon::parse($request->from)->format('d-M-y') . "</h3>";
        $htmlBody .= "<ul>";
        foreach ($dummy_array as $group) {
            $htmlBody .= "<li><strong>From:</strong> {$group['start']} <strong>To:</strong> {$group['end']} —
                      <a href=\"{$group['link']}\">View</a></li>";
        }
        $htmlBody .= "</ul>";

        Mail::html($htmlBody, function ($message) {
            $message->to([
                'jks_support@datavoice.co.in',
                'temkarsiddharth1999@gmail.com',
            ])
            ->cc('lakshadeep@datavoice.co.in')
            ->subject('VVMT Ticket Links');
        });

        return response()->json(['message' => 'Email sent successfully.']);
    }
}
