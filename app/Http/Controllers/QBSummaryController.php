<?php

namespace App\Http\Controllers;
use ZipArchive;
use App\Summary;
use App\Models\Level;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Summary as AppSummary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreQuestionBankRequest;
use App\Http\Controllers\QuestionBankController;

class QBSummaryController extends Controller
{
     protected $questionBankController;
    protected $summaryController;
     public function __construct(QuestionBankController $questionBankController,SummaryController $summaryController)
    {
        $this->questionBankController = $questionBankController;
        $this->summaryController = $summaryController;
    }
    public function create()
    {
        return view('qb_summary.create');
    }

    public function store(StoreQuestionBankRequest $request)
    {
        try{
        $zipFile = $request->file('fileupload');
        $zipPath = $zipFile->getPathname();

        $zip = new ZipArchive;
        $docFile = null;
        $excelFile = null;

        if ($zip->open($zipPath) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                // Check if file has DOC or DOCX extension and ensure only one file is selected
                if (preg_match('/\.(doc|docx)$/i', $filename)) {
                    if (!$docFile) {
                        $docFile = $filename; // Store single DOCX file
                    } else {
                        return back()->withErrors(['fileupload' => 'Only one DOC/DOCX file is allowed.']);
                    }
                }
            }
            $zip->close();
        }

        if (!$docFile) {
            return back()->withErrors(['fileupload' => 'No DOC/DOCX file found in the ZIP.']);
        }

        if (strlen($docFile) < 10) {
            return back()->withErrors(['fileupload' => 'Invalid DOC file naming convention.']);
        }

        // Extract the first 10 characters for processing
        $idString = substr($docFile, 0, 10);

        // Extract files to a temporary storage path
        $extractPath = storage_path('app/temp/');

        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($extractPath, $docFile);
            $zip->extractTo($extractPath, $excelFile);
            $zip->close();
        }

        // Ensure the extracted files exist
        $docFilePath = $extractPath . $docFile;

        // Create a new UploadedFile instance for the DOC file
        $uploadedDocFile = new UploadedFile(
            $docFilePath,
            $docFile,
            mime_content_type($docFilePath),
            null,
            true
        );

        // Process the extracted DOC file (Summary Controller)
        $summary_req = new Request([
            'fileInput' => $uploadedDocFile,
            'course'  => intval(substr($idString, 0, 2)),
            'level'   => intval(substr($idString, 2, 2)),
            'attempt' => intval(substr($idString, 4, 2)),
            'subject' => intval(substr($idString, 6, 2)),
            'chapter' => intval(substr($idString, 8, 2)),
        ]);

        //Course Check
        $course = Course::where('id', $summary_req['course'])
                        ->where('status', Course::ACTIVE)
                        ->first();

        if(empty($course)){
            return redirect()->route('question.bank')->with('error',"Course not found in filename.");
        }

        //Level Check
        $level = Level::where('id', $summary_req['level'])
                        ->where('status', Level::ACTIVE)
                        ->first();

        if(empty($level)){
            return redirect()->route('question.bank')->with('error',"Level not found in filename.");
        }

         //Subject Check
         $subject = Subject::where('id', $summary_req['subject'])
                        ->where('status', Subject::ACTIVE)
                        ->first();

        if(empty($subject)){
            return redirect()->route('question.bank')->with('error',"Subject not found in filename.");
        }

        //Chapter Check
        $chapter = Chapter::where('id', $summary_req['chapter'])
                        ->where('status', Chapter::ACTIVE)
                        ->first();

        if(empty($chapter)){
            return redirect()->route('question.bank')->with('error',"Chapter not found in filename.");
        }

        $summary = Summary::where('course_id', $summary_req['course'])
                        ->where('level_id', $summary_req['level'])
                        ->where('subject_id', $summary_req['subject'])  
                        ->where('chapters_id', $summary_req['chapter'])
                        ->first();

        // if(empty($summary))
        // {
        //     $summaryResponse = $this->summaryController->addSummary($summary_req);
        //     if (!$summaryResponse) {
        //         return redirect()->route('question.bank')->with('error',"Failed to process the summary.");
        //     }  
        // }  

        $reult = $this->questionBankController->store($request);
        // Optionally, delete the extracted files after processing
        unlink($docFilePath);

        return redirect()->route('question.bank')->with('success',"Question Bank & Summary Upload Successfully.");
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
