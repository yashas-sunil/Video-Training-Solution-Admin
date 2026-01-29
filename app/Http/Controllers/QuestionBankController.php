<?php

namespace App\Http\Controllers;

use App\Question;
use App\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreQuestionBankRequest;

class QuestionBankController extends Controller
{
    // rotected $question;
    protected $questionBank;
    public function __construct(QuestionBank $questionBank,Question $question)
    {
        $this->questionBank=$questionBank;
        $this->question=$question;
    }
    public function index()
    {
        $questionbank = QuestionBank::all();
        return view('questionbank.index',compact('questionbank'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages=Language::where('status',Language::ACTIVE)->orderBy('name','asc')->get();
        return view('questionbank.create',compact('languages'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreQuestionBankRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storecopy(StoreQuestionBankRequest $request)
    {
        try {
            $validated= $request->validated();

          
            $file_details=  $validated['fileupload'];
            $get_filename = $file_details->getClientOriginalName();
             $name='jeswill';
            $history_folder_name  = $name.'_'.time();
            $history_storage_path = storage_path('app/public/questionbank/upload/'.$history_folder_name);
            $storage_folder_Path  = storage_path('app/public/questionbank/'.$name.'/');
           
            $save_File            = $file_details->move($history_storage_path, $get_filename);
           
             // $logFiles = Zipper::make($history_storage_path.'/'.$get_filename)->listFiles();
            
             $zip = new ZipArchive();
             $x = $zip->open($history_storage_path.'/'.$get_filename,ZipArchive::CREATE);
             if ($x === true) {
                 $zip->extractTo($storage_folder_Path);
                 $zip->close();
             }

            // $result=$this->questionBank->uploadQuestionBank($validated,$header='',$folder='');
      
        } catch (\Throwable $th) {
            return redirect()->back()->with('error',$th->getMessage());
        }
       
    }

   public function store(StoreQuestionBankRequest $request)
{
    Log::info('QB STEP 1: QuestionBankController@store HIT');
    Log::info('QB STEP 1.1: Raw Request Data', $request->all());
    Log::info('QB STEP 1.2: Request Files', $request->files->all());

    try {
        ini_set('max_execution_time', '3000');
        Log::info('QB STEP 2: max_execution_time set');

        // ===== VALIDATION PART =====
        if (!isset($request->championship)) {
            Log::info('QB STEP 3: championship NOT set → running validated()');

            $validated = $request->validated();

            Log::info('QB STEP 3.1: validation PASSED', $validated);
        } else {
            Log::info('QB STEP 3: championship SET → using full request');
            $validated = $request->all();
        }

        // ===== BEFORE UPLOAD =====
        Log::info('QB STEP 4: Before uploadQuestionBank()', [
            'validated_keys' => array_keys($validated),
            'has_file' => isset($validated['file']) || isset($validated['fileupload']),
        ]);

        $result = $this->questionBank->uploadQuestionBank(
            $validated,
            $header = '',
            $folder = ''
        );

        Log::info('QB STEP 5: uploadQuestionBank() RETURNED', $result);

        $success = '';

        // ===== CHAMPIONSHIP FLOW =====
        if (isset($result['success_message']) && isset($request->championship)) {
            Log::info('QB STEP 6: championship success flow');

            $success .= "Please wait your question Bank is uploading and championship is being created. Please check your email for question bank upload and championship creation status";
            return $success;
        }

        if (isset($result['error_message']) && isset($request->championship)) {
            Log::error('QB STEP 6.1: championship error flow', [
                'error' => $result['error_message']
            ]);

            $success .= $result['error_message'];
            return $success;
        }

        // ===== NORMAL FLOW =====
        if (isset($result['success_message'])) {
            Log::info('QB STEP 7: success_message found', [
                'message' => $result['success_message']
            ]);

            $success .= $result['success_message'];
        }

        if (isset($result['error_message'])) {
            Log::error('QB STEP 7.1: error_message found', [
                'message' => $result['error_message']
            ]);

            $success .= $result['error_message'];
        }

        Log::info('QB STEP 8: BEFORE FINAL REDIRECT', [
            'final_message' => $success
        ]);

        return redirect()
            ->route('question.bank')
            ->with('success', $success);

    } catch (\Throwable $th) {
        // dd($th);

        Log::error('QB STEP 9: EXCEPTION CAUGHT', [
            'message' => $th->getMessage(),
            'file' => $th->getFile(),
            'line' => $th->getLine(),
            'trace' => $th->getTraceAsString(),
        ]);

        return redirect()
            ->back()
            ->with('error', $th->getMessage());
    }
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionBank $questionBank)
    {
       
        $questionsList= $this->question->fetchQuestionsByQuestionBankId($questionBank);
        // dd($questionsList);
        // $questionsList=(object)$questionsList;
        return view('questionbank.show',compact('questionBank','questionsList')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function edit(QuestionBank $questionBank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuestionBankRequest  $request
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuestionBankRequest $request, QuestionBank $questionBank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuestionBank $questionBank)
    {
        //
    }
}
