<?php

namespace App;

use ZipArchive;
use App\Championship;
use App\Jobs\QuestionBankUpload;
use App\Models\QuestionBankHistory;
use App\QuestionBankHistory as AppQuestionBankHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionBank extends Model
{
    //  use App\HasFactory;
    protected $guarded = ['id'];
    const ACTIVE = 1;
    const INACTIVE = 0;

     public function uploadQuestionBank($input, $header = '', $folder = '')
    {
        $storage = null;
        $insert_data = array();
        $result_show = array();
        $error       = array();
        $history_id  = 0;

        $file_details = $this->zipextension($input);

        $question   = new Question();
        $answer     = new Answer();
        $qbhistory  = new AppQuestionBankHistory();
        $updated_by = Auth::User()->id;
        $mailemail  = Auth::User()->email;
        if (($file_details['error'] == 1)) {

            $error['error_message'] = $file_details['error_message'];
            return $error;
        } else {



            $qbid = '';

            if (!empty($input['name'])) {
                $qbname = trim(strtolower($input['name']));
                $insert_qb = $this->insertQuestionBank($input);
                $qbid = $insert_qb;
                $folder_exists = Storage::disk('public')->exists('questionbank/' . $insert_qb . '/');
                $history_id = $qbhistory->addQuestionBankHistory($input, $insert_qb, 1);
                if ($folder_exists == false) {

                    $folderpath = 'question/' . $insert_qb . '/0';
                    $folder = Storage::disk('public')->put($folderpath, '');

                    $storage = $insert_qb;
                }
            } else {

                $qbid = $input['qbid'];
                $qbname = trim($this->fetchQuestionBankbyId($qbid));
                $storage = $qbid;
                $insert_qb = $input['qbid'];
                $update_qb = $this->updateQuestionBank($input);
                $history_id = $qbhistory->addQuestionBankHistory($input, $qbid, 1);

                $folder_exists = Storage::disk('public')->exists('questionbank/' . $qbid . '/');

                if (!empty($folder_exists)) {
                } else {

                    Storage::disk('public')->makeDirectory('questionbank/' . $qbid);
                }
            }


            $exactzip = $this->exactZip($file_details['data'], $storage);

            if (($exactzip['error'] == 1)) {

                if (!empty($input['name'])) {
                    Log::info('inside question bank 2');
                    AppQuestionBankHistory::where('question_banks_id', $qbid)->delete();
                    $delete = QuestionBank::where('id', '=', $qbid)->delete();
                }
                $history = $qbhistory->updateQuestionBankHistory($input, $qbid, 3, $history_id, $filepath = '', $message = $exactzip['error_message']);

                $error['error_message'] = $exactzip['error_message'];

                return $error;
            } else {

                $filePath                  = $exactzip['dummypath'];
                $storage_folder_Path       = $exactzip['storage_folder_Path'];
                $storage_dummy_folder_Path = $exactzip['storage_dummy_folder_Path'];
                $storage_dummy_folder_name = $exactzip['storage_dummy_folder_name'];
                Log::info('eden start');
                $readexcel = $this->readExcel($filePath);

                if (empty($readexcel['error'] == 0)) {
                    Log::info('eden in error');
                    $error['error_message']   = $readexcel['error_message'];
                    return $error;
                } else {

                    Log::info('eden outside error');
                    $input_array = array();
                    // $input_array['question_type']  = $input['question_type'];
                    $input_array['category']       = $input['category'];

                    if (isset($input['championship'])) {                   //If Question bank upload is initiated via championship
                        $input_array['chapters'] = $input['chapters'];    //Get chapters to check that no other chapters other than selected chapters are present in championship
                        $input_array['name'] = $input['name_to_store'];  //Get Championship name to be included in creation mail
                        $championship_data=$input['championship_data']; //apart from file uplod (championship) contains all data

                        if(isset($input['subjective_pdf_upload'])){   //if subjective question pdf present store it

                            $upload_question_pdf=Championship::uploadQuestionPDF($input['subjective_pdf_upload']); //store pdf
                        
                            if($upload_question_pdf['status'] == 200){    //if successfully stored, add subjective question pdf name to input to store in championship tab;e
                                $input_array['subjective_pdf_upload']= $upload_question_pdf['file_name'];
                            }else{                                      //if error occurs return back along with error
                                $error['error_message'] = $upload_question_pdf['error'];
                                return  $error;
                            }
                        }else{
                            $input_array['subjective_pdf_upload']=null;
                        }
                      
                    }else{
                        $championship_data=null;    //If Question bank upload is initiated via create qb (to ensure championship code is not executed)
                    }

                    // $input_array['languages_id']    = $input['languages_id'];
                    $type                          = $input['category'];
                    // dd($readexcel);
                    try{
                    $will =    QuestionBankUpload::dispatch($readexcel, $qbid, $input_array, $storage, $storage_folder_Path, $storage_dummy_folder_Path, $type, $storage_dummy_folder_name, $insert_qb, $updated_by, $mailemail, $history_id,$championship_data);       
                    } catch (\Exception $e) {
                        dd($e);
                        Log::info("Qb Upload Dispatch Issue" . $e->getMessage());
                    }           
                    //  $will=    QuestionBankUpload::dispatch($readexcel,$qbid,$input_array,$storage,$storage_folder_Path,$storage_dummy_folder_Path,$type,$storage_dummy_folder_name,$insert_qb,$updated_by,$mailemail,$history_id);
                    //  Log::info(' will jobs');
                    // echo $will;
                    // exit;
                    $readexcel['error_message'] = '';
                    $readexcel['success_message'] = "Please wait your question Bank is uploading. Please check your email for question bank upload status";
                    $error['success_message'] = $readexcel['success_message'];
                    return $error;
                    // return $will;

                }
            }
        }
    }

      public function exactZip($file_details, $name)
    {
        $error = array();
        $get_filename = $file_details->getClientOriginalName();

        $history_folder_name  = $name . '_' . time();
        $history_storage_path = storage_path('app/public/questionbank/upload/' . $history_folder_name);
        $storage_folder_Path  = storage_path('app/public/questionbank/' . $name . '/');

        $save_File            = $file_details->move($history_storage_path, $get_filename);

        // $logFiles = Zipper::make($history_storage_path.'/'.$get_filename)->listFiles();

        $zip = new ZipArchive();
        $x = $zip->open($history_storage_path . '/' . $get_filename, ZipArchive::CREATE);
        if ($x === true) {
            $zip->extractTo($history_storage_path);
            $zip->close();
        }
        //  Zipper::make($history_storage_path.'/'.$get_filename)->extractTo($history_storage_path);
        //  Zipper::close();

        $filename = $history_storage_path . '/' . $get_filename;
        File::delete($filename);

        // $url        =Storage::disk('local')->url($name . '/');
        $file_names = File::files($history_storage_path);



        foreach ($file_names as $file) {

            if ($file->getExtension() == 'xlsx' || $file->getExtension() == 'xls') {

                $error['error']               = 0;
                $error['error_message']       = '';
                $error['data']                = $file->getFilename();
                $error['dummypath']           = $history_storage_path . '/' . $file->getFilename();
                $error['storage_folder_Path'] = $storage_folder_Path;
                $error['storage_dummy_folder_Path'] = $history_storage_path;
                $error['storage_dummy_folder_name'] = $history_folder_name;

                return $error;
            }
        }
        $error['error']         = 1;
        $error['error_message'] = 'Excel file Not Found';
        $error['data']           = '';
        return $error;
    }

     public function readExcel($filepath)
    {

        $error = array();
        if (!empty($filepath)) {

            $excelarray = array();
            $obj_PhpOffice = IOFactory::load($filepath);
            $objWorksheet = $obj_PhpOffice->setActiveSheetIndex(0);
            $sheet_data = $objWorksheet->rangeToArray('A1:BZ5000', null, true, true, true);

            $sheet_data = array_filter($sheet_data);
            foreach ($sheet_data as $each_arr_k => $each_arr_v) {
                $flag = 1;
                foreach ($each_arr_v as $val) {
                    if (!empty($val)) {
                        $flag++;
                    }
                }
                if ($flag == 1) {
                    unset($sheet_data[$each_arr_k]);
                }
            }

            $obj_PhpOfficess = new Spreadsheet();
            $obj_PhpOfficess->setActiveSheetIndex(0);
            $obj_PhpOfficess->getDefaultStyle()
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);


            $style_array = array(
                'fill' => array(
                    'fillType' => Fill::FILL_SOLID,
                    'color' => array('argb' => '32cd32')
                )
            );
            $style_array_error = array(
                'fill' => array(
                    'fillType' => Fill::FILL_SOLID,
                    'color' => array('argb' => 'ffff00')
                )
            );

            $error_column_style_array = array(
                'fill' => array(
                    'fillType' => Fill::FILL_SOLID,
                    'color' => array('argb' => 'ff0000')
                )
            );
            $excelarray['sheet_data'] = $sheet_data;
            $excelarray['style_array'] = $style_array;
            $excelarray['style_array_error'] = $style_array_error;
            $excelarray['error_column_style_array'] = $error_column_style_array;
            // $excelarray['obj_PhpOfficess'] = $obj_PhpOfficess;
            $excelarray['error'] = 0;

            return $excelarray;
        }
    }
    public function zipextension($input)
    {
        $error = array();
        $file_details = $input['fileupload'];
        $file_extension = $file_details->getClientOriginalExtension();
        if ($file_extension != 'zip') {
            $error['error'] = 1;
            $error['error_message'] = 'Choose only ZIP file!';
        } else {

            $error['error'] = 0;
            $error['error_message'] = '';
            $error['data'] = $file_details;
        }

        return $error;
    }

    public function insertQuestionBank($input)
    {
        $data = new QuestionBank();
        $data->name = $input['name'];

        if (isset($input['status'])) {
            $data->status = QuestionBank::ACTIVE;
        } else {
            $data->status = QuestionBank::INACTIVE;;
        }
        // $data->languages_id = $input['languages_id'];

        $data->created_by = Auth::user()->id;
        $data->updated_by = Auth::user()->id;
        $data->save();
        if ($data->id) {
            return $data->id;
        } else {
            return 0;
        }
    }
    
}
