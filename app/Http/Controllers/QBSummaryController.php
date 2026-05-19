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
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreQuestionBankRequest;
use App\Http\Controllers\QuestionBankController;
use App\ScormPackage;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\PhpWord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\QuestionBank;
use App\QuestionBankHistory as AppQuestionBankHistory;
use App\Models\Quiz\Question as QuizQuestion;
use App\AppAnswer;
use App\Solution;
use App\ChaptersQuestion as AppChaptersQuestion;

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
    // Active courses fetch karaycha for dropdown
    $courses = ScormPackage::where('status', 1)->get();

    // View la courses pass kela
    return view('qb_summary.create', compact('courses'));
}

    public function store(StoreQuestionBankRequest $request)
    {
    //    dd($request->all());
       if ($request->hasFile('fileupload') && $request->file('fileupload')->getMimeType() == 'application/zip'){
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

        // if(empty($course)){
        //     return redirect()->route('question.bank')->with('error',"Course not found in filename.");
        // }

        //Level Check
        $level = Level::where('id', $summary_req['level'])
                        ->where('status', Level::ACTIVE)
                        ->first();

        // if(empty($level)){
        //     return redirect()->route('question.bank')->with('error',"Level not found in filename.");
        // }

         //Subject Check
         $subject = Subject::where('id', $summary_req['subject'])
                        ->where('status', Subject::ACTIVE)
                        ->first();

        // if(empty($subject)){
        //     return redirect()->route('question.bank')->with('error',"Subject not found in filename.");
        // }

        //Chapter Check
        $chapter = Chapter::where('id', $summary_req['chapter'])
                        ->where('status', Chapter::ACTIVE)
                        ->first();

        // if(empty($chapter)){
        //     return redirect()->route('question.bank')->with('error',"Chapter not found in filename.");
        // }

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
             //dd($th);
        }
       }else{

         $validated = $request->validate([
            'fileupload' => 'required|file|mimes:pdf|max:20480',
            ]);
            $url = env('PDF_TEXT_URL');
           $file = $request->file('fileupload');

            $response = Http::acceptJson()
                ->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post($url);

            $extractedQuestions = $response->json();

            // Validate API response
            if (empty($extractedQuestions['status']) || empty($extractedQuestions['data'])) {
                return back()->withErrors(['fileupload' => 'Could not extract questions from PDF. Please check the PDF format.']);
            }

            // Store questions using the same pattern as Excel import
            return $this->storePdfQuestions($request, $extractedQuestions['data']);
       }
    }

    /**
     * Get subjects by course ID - AJAX endpoint
     */
    public function getSubjectsByCourse(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json(['subjects' => []]);
        }

        // Fetch subjects for the given course
        $subjects = Subject::where('course_id', $courseId)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'subjects' => $subjects
        ]);
    }

    /**
     * Get chapters by course and subject - AJAX endpoint
     */
    public function getChaptersBySubject(Request $request)
    {
        $courseId = $request->get('course_id');
        $subjectId = $request->get('subject_id');
        
        if (!$courseId || !$subjectId) {
            return response()->json(['chapters' => []]);
        }

        // Fetch chapters for the given course and subject
        $chapters = Chapter::where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'chapters' => $chapters
        ]);
    }

    /**
     * Store PDF-extracted questions into DB (same schema as Excel import)
     *
     * @param StoreQuestionBankRequest $request
     * @param array $questions - Array of questions from external API
     * @return \Illuminate\Http\RedirectResponse
     */
    private function storePdfQuestions(StoreQuestionBankRequest $request, array $questions)
    {
        try {
            $userId = Auth::id();
            $now = Carbon::now();

            // 1. Create QuestionBank record (same as insertQuestionBank)
            $qb = new QuestionBank();
            $qb->name = $request->input('name');
            $qb->status = $request->has('status') ? QuestionBank::ACTIVE : QuestionBank::INACTIVE;
            $qb->created_by = $userId;
            $qb->updated_by = $userId;
            $qb->status = 1;
            $qb->save();
            $qbId = $qb->id;

            // 2. Create QuestionBankHistory record
            $qbHistory = new AppQuestionBankHistory();
            $historyId = $qbHistory->addQuestionBankHistory(
                ['name' => $request->input('name'), 'category' => $request->input('category', 1)],
                $qbId,
                1
            );

            // 3. Get form dropdown values
            $courseId = $request->input('course_id');
            $subjectId = $request->input('subject_id');
            $chapterId = $request->input('chapter_id');
            $difficultyLevel = $request->input('difficulty_level');

            // 4. Build insert arrays
            $result = $this->buildPdfInsertData(
                $questions, $qbId, $courseId, $subjectId, $chapterId, $difficultyLevel, $userId, $now
            );

            // 5. Bulk insert in transaction
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::beginTransaction();

            try {
                $questionModel = new QuizQuestion();
                $answerModel = new AppAnswer();
                $solutionModel = new Solution();
                $chapterQuestionModel = new AppChaptersQuestion();

                $questionModel->bulkQuestionUpload($result['questions']);
                $answerModel->bulkAnswersUpload($result['answers']);

                if (!empty($result['solutions'])) {
                    $solutionModel->bulkSolutionsUpload($result['solutions']);
                }

                $chapterQuestionModel->bulkChapterQuestionUpload($result['chapter_questions']);

                DB::commit();
                Log::info('PDF Import: Successfully inserted ' . count($result['questions']) . ' questions for QB ID: ' . $qbId);

            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('PDF Import: Bulk insert failed', [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]);

                // Cleanup: delete the QB record since insert failed
                AppQuestionBankHistory::where('question_banks_id', $qbId)->delete();
                QuestionBank::where('id', $qbId)->delete();

                return back()->withErrors(['fileupload' => 'Failed to save questions: ' . $e->getMessage()]);
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            // 6. Update history as successful
            $qbHistory->updateQuestionBankHistory(
                ['name' => $request->input('name'), 'category' => $request->input('category', 1)],
                $qbId,
                4,
                $historyId,
                '',
                'PDF QuestionBank uploaded successfully - ' . count($result['questions']) . ' questions'
            );

            return redirect()->route('question.bank')
                ->with('success', count($result['questions']) . ' Questions uploaded successfully from PDF.');

        } catch (\Throwable $th) {
            Log::error('PDF Import: storePdfQuestions failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            return back()->withErrors(['fileupload' => 'Error processing PDF: ' . $th->getMessage()]);
        }
    }

    /**
     * Build insert data arrays from PDF API response
     *
     * @param array $questions - API response data array
     * @param int $qbId - Question Bank ID
     * @param int $courseId
     * @param int $subjectId
     * @param int $chapterId
     * @param int $difficultyLevel
     * @param int $userId
     * @param Carbon $now
     * @return array ['questions' => [], 'answers' => [], 'solutions' => [], 'chapter_questions' => []]
     */
    private function buildPdfInsertData($questions, $qbId, $courseId, $subjectId, $chapterId, $difficultyLevel, $userId, $now)
    {
        $questionModel = new QuizQuestion();
        $answerModel = new AppAnswer();
        $solutionModel = new Solution();
        $chapterQuestionModel = new AppChaptersQuestion();

        // Get current max IDs
        $questionId = $questionModel->search_max_question_id() + 1;
        $questionNumber = $questionModel->search_max_question_number() + 1;
        $answerId = $answerModel->search_max_question_answer_id() + 1;
        $solutionId = $solutionModel->searchMaxSolutionId() + 1;
        $chapterQuestionId = $chapterQuestionModel->findMaxChapterQuestionId() + 1;

        $insertQuestions = [];
        $insertAnswers = [];
        $insertSolutions = [];
        $insertChapterQuestions = [];

        // Option letter to number mapping
        $optionMap = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6];

        foreach ($questions as $q) {
            $currentQuestionId = $questionId++;
            $currentQuestionNumber = $questionNumber++;

            // Convert correct_option letter to number (A=1, B=2, etc.)
            $correctOptionLetter = strtoupper(trim($q['correct_option'] ?? ''));
            $correctAnswer = $optionMap[$correctOptionLetter] ?? 1;

            // --- Question row ---
            $insertQuestions[] = [
                'id'                  => $currentQuestionId,
                'question_number'     => $currentQuestionNumber,
                'question_banks_id'   => $qbId,
                'course_id'           => $courseId,
                'subject_id'          => $subjectId,
                'chapter_id'          => $chapterId,
                'difficult_levels_id' => $difficultyLevel,
                'question'            => $q['question_text'] ?? '',
                'correct_answer'      => $correctAnswer,
                'source'              => 'PDF Import',
                'status'              => 1,
                'created_by'          => $userId,
                'updated_by'          => $userId,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];

            // --- Answer rows ---
            $optionIndex = 1;
            foreach ($q['options'] ?? [] as $optionLetter => $optionText) {
                $currentAnswerId = $answerId++;
                $isCorrect = (strtoupper($optionLetter) === $correctOptionLetter) ? 1 : 0;

                $insertAnswers[] = [
                    'id'              => $currentAnswerId,
                    'question_id'     => $currentQuestionId,
                    'answer'          => $optionText,
                    'correctans'      => $isCorrect,
                    'created_by'      => $userId,
                    'updated_by'      => $userId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                $optionIndex++;
            }

            // --- Solution row (if solution_text exists) ---
            if (!empty($q['solution_text'])) {
                $currentSolutionId = $solutionId++;
                $insertSolutions[] = [
                    'id'          => $currentSolutionId,
                    'question_id' => $currentQuestionId,
                    'name'        => $q['solution_text'],
                    'status'      => Solution::ACTIVE,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // --- Chapter Question row ---
            $currentChapterQuestionId = $chapterQuestionId++;
            $insertChapterQuestions[] = [
                'id'          => $currentChapterQuestionId,
                'chapter_id'  => $chapterId,
                'question_id' => $currentQuestionId,
                'status'      => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        return [
            'questions'         => $insertQuestions,
            'answers'           => $insertAnswers,
            'solutions'         => $insertSolutions,
            'chapter_questions' => $insertChapterQuestions,
        ];
    }
}
