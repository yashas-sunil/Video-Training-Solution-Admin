<?php

namespace App\Models\Quiz;

use DB;
use File;
use Storage;
use Madzipper;
use Validator;
use App\Answer;
use App\AnswerType;
use App\Subchapter;
use App\DifficultLevel;
use App\Models\Chapter;
use App\Models\Subject;
use App\Jobs\QuestionAnswerUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Modules\Master\Entities\Languages;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
// use Illuminate\Support\Facades\Redirect;
// use Spatie\Activitylog\Traits\LogsActivity;

class Question extends Model
{
    use SoftDeletes;

    protected $table = 'questions';

    protected $primaryKey = 'id';

    protected static $logAttributes = ['*'];

    public function zipextension($input) {
        $error = array();
        $file_details = $input['q_attachment'];
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

    public function ExactZip($file_details) {
        // upload_max_filesize = 10M
        
        $error = array();
        $get_filename = $file_details->getClientOriginalName();

        // dd($get_filename);
        // $history_folder_name  = $name.'_'.time();
        // $history_storage_path = storage_path('app/public/questionbank/upload/'.$history_folder_name);
        $storage_folder_Path  = storage_path('app/public/Documents/ExcelSample/');

        $save_File            = $file_details->move($storage_folder_Path, $get_filename);
        //remove same directory if exixst
        if (is_dir(str_replace('.zip', '', $storage_folder_Path.$get_filename.'/'))){
            system("rm -rf ".escapeshellarg(str_replace('.zip', '', $storage_folder_Path.$get_filename.'/')));
            // rmdir(str_replace('.zip', '', $storage_folder_Path.$get_filename.'/'));
        }
         Madzipper::make($storage_folder_Path.'/'.$get_filename)->extractTo($storage_folder_Path);
         Madzipper::close();

        $filename = $storage_folder_Path . '/' . $get_filename;

        \File::delete($filename);


        $url        = \Storage::disk('local')->url('ExcelSample/');
        $storage_folder_Path = str_replace('.zip', '', $storage_folder_Path.$get_filename.'/');
        // echo "<pre>"; print_r($storage_folder_Path); echo "</pre>"; die('anil testing'); echo date("l jS \of F Y h:i:s A");
        
        $file_names = File::files($storage_folder_Path);

         foreach ($file_names as $file) {

            if ($file->getExtension() == 'xlsx' || $file->getExtension() == 'xls') {

                 $error['error']               = 0;
                 $error['error_message']       = '';
                 $error['file_name']           = $file->getFilename();
                 $error['dummypath']           = $storage_folder_Path.'/'.$file->getFilename();
                 $error['storage_folder_Path'] = $storage_folder_Path;
                 $error['storage_dummy_folder_Path'] = $storage_folder_Path;
                return $error;


            }
        }
            $error['error']         = 1;
            $error['error_message'] = 'Excel file Not Found';
            $error['data']           = '';
            return $error;
    }

    public function Readexcel($filepath) {

        $error = array();
        if (!empty($filepath)) {

            $excelarray = array();
            $obj_PhpOffice = IOFactory::load($filepath);
            $objWorksheet = $obj_PhpOffice->setActiveSheetIndex(0);
            $sheet_data = $objWorksheet->rangeToArray('A1:AU5000', null, true, true, true);
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

            $obj_PhpOffice = new Spreadsheet();
            $obj_PhpOffice->setActiveSheetIndex(0);
            $obj_PhpOffice->getDefaultStyle()
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
            $excelarray['obj_PhpOffice'] = $obj_PhpOffice;
            $excelarray['error'] = 0;
            // dd($excelarray);
            return $excelarray;
        }
    }

    public function uploadQuestion($input){

        $error       = array();
        $file_details = $this->zipextension($input);
        // dd($file_details);
        if (($file_details['error'] == 1)) {

             $error['error_message']=$file_details['error_message'];
             return $error;

        } else {

            $exactzip = $this->ExactZip($file_details['data']);

            if (($exactzip['error'] == 1)) {
                 $error['error_message']=$exactzip['error_message'];

                return $error;

             } else {

            $filePath                  = $exactzip['dummypath'];
            $storage_folder_Path       = $exactzip['storage_folder_Path'];
            $storage_dummy_folder_Path = $exactzip['storage_dummy_folder_Path'];
            $file_name = $exactzip['file_name'];

            $readexcel = $this->Readexcel($filePath);
            // dd($readexcel);
            if (empty($readexcel['error'] == 0)) {

                  $error['error_message']   = 'File is  unreadable.';
                  return $error;

            } else {

                $error['readexcel'] = $readexcel; 
                $error['storage_folder_Path'] = $storage_folder_Path; 

                // QuestionAnswerUpload::dispatch($readexcel,$storage_folder_Path);
       
             $error['error_message']='File Uploaded Successfully';
             return $error;

            }
            }


        }
    }

    public function getOptions(){
        return $this->hasMany('App\Models\Quiz\Answer','question_id','id')->orderBy('order_by', 'ASC');
    }

    public function getRandomOptions(){
        return $this->hasMany('App\Models\Quiz\Answer','question_id','id')->inRandomOrder();
    }

    public function getGrade(){
        return $this->belongsTo('App\Models\Quiz\Grade','grade_id','id');
    }

    public function getBoard(){
        return $this->belongsTo('App\Models\Quiz\Board','board_id','id');
    }

    public function getParagraph(){
        return $this->belongsTo('App\Models\Quiz\Paragraph','paragraph_id','id');
    }

    public function getInstruction(){
        return $this->belongsTo('App\Models\Quiz\Instruction','instruction_id','id');
    }

    public function getConcept(){
        return Question::belongsTo('App\Models\Quiz\Concept','concept_id','id');
    }

    public function getSubject(){
        return Question::belongsTo('App\Models\Quiz\Subjects','subject_id','id');
    }

    public function getChapter(){
        return Question::belongsTo('App\Models\Quiz\Chapters','chapter_id','id');
    }

    public function getRandomOptionsLimits(){
        return $this->hasMany('App\Models\Quiz\Answer','question_id','id')->inRandomOrder()->where('is_correct',0)->limit(2);
    }

     public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
     public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
    }
     public function subChapter()
    {
        return $this->belongsTo(Subchapter::class, 'subchapter_id', 'id');
    }
 public function answerType()
    {
        return $this->belongsTo(AnswerType::class, 'answer_types_id', 'id');
    }
    public function difficultLevel()
    {
        return $this->belongsTo(DifficultLevel::class, 'difficult_level_id', 'id');
    }

     public function userAttempts()
    {
        return $this->hasMany(UserAnswers::class, 'question_id');
    }
}
