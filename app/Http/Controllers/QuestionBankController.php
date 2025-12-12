<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        try {
            ini_set('max_execution_time', '3000'); 
            if(!isset($request->championship)) //if championship directly store request data to validated
            { 
                $validated= $request->validated();
            }
            else
            {
                $validated=$request->all();
            }

            $result=$this->questionBank->uploadQuestionBank($validated,$header='',$folder='');
         
            $success='';
            if(isset($result['success_message']) && isset($request->championship)) //if no error occurs, dosen't matter if championship created or not will be notified via mail
            {
                $success.="Please wait your question Bank is uploading and championship is being created. Please check your email for question bank upload and championship creation status";
                return $success;
            }

            if(isset($result['error_message']) && isset($request->championship)) //if any error occurs show error (eg. subjective pdf not uploaded)
            {
                $success.=$result['error_message'];
                return $success;
            }

            if(isset($result['success_message']))
            {
               $success.= $result['success_message'];
            }

            if(isset($result['error_message']))
            {
                $success.=$result['error_message'];
            }
            return redirect()->route('question-bank.index')->with('success',$success);
    //  print_r($result);
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('error',$th->getMessage());
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
