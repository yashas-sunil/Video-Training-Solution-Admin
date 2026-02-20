<?php

namespace App\Models\Quiz;

use App\Answer;
use App\AnswerType;
use App\DifficultLevel;
use App\Jobs\QuestionAnswerUpload;
use App\Models\Chapter;
use App\Models\Subject;
use App\Solution;
use App\Subchapter;
use DB;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Madzipper;
use Modules\Master\Entities\Languages;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Storage;
use Validator;
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

      public function search_max_question_number()
    {
        return $result = Question::max('question_number');
    }
    public function search_max_question_id()
    {
        return $result = Question::max('id');
    }
    public function bulkQuestionUpload($insert_data)
    {

        return $result = Question::insert($insert_data);
    }
    public function getQuestionId($qb_id)
    {

        return Question::where('question_banks_id', $qb_id)->select('id')->get()->toArray();
    }
    
     public function fetchQuestionsByQuestionBankId($questionBank)
    {
        $questionList = Question::with(
            'course',
            'level',
            'subject',
            'topicQuestion',
            'chapter',
            'summary',
            'concept',
            'solution.solutionImages',
            'difficultLevel',
            'author',
            'reviewer',
            'answerType',
            'questionType',
            'createdBy',
            'updatedBy'
        )
            ->where('question_banks_id', $questionBank->id)->get();

        // dd($questionList);
        $answer = new Answer();

        if (!empty($questionList)) {
            $question = array();
            $i = 1;
            foreach ($questionList as $results) {

                $temp = array();
                $temp['srno'] = $i;
                $temp['id'] = $results->id;
                $temp['question_number'] = $results->question_number;
                $temp['course_name'] = $results->course->name;
                $temp['level_name'] = $results->level->name;
                $temp['subject_name'] = $results->subject->name;
                $temp['chapter_name'] = $results->chapter->name;
                $temp['question'] =   !empty($results->question) ? $results->question : '-';
                $temp['summary_name'] =   !empty($results->summary->name) ? $results->summary->name : '-';
                $content_array = [];
                foreach ($results->topicQuestion as $content_val) {
                    foreach ($content_val->content as $content_name) {
                        $content_array[] = $content_name->name;
                    }
                }

                $temp['content_name'] = $content_array ?? null;
                $temp['importance'] = $results->importance;
                $temp['source'] = $results->source;
                $temp['solution_name'] =   !empty($results->solution->name) ? $results->solution->name : '-';
                $temp['solution_id'] =  !empty($results->solution->id) ? $results->solution->id : 0;
                $temp['solution_image'] =   !empty($results->solution->name) ? $results->solution->name : '-';
                $concept_array = [];
                $concept_note_array = [];
                foreach ($results->concept as $concept_val) {
                    $concept_array[] = $concept_val->audio->flash_card_name;
                    $concept_note_array[] = $concept_val->audio->audio_content;
                }

                $temp['concept_name'] = $concept_array ?? null;
                $temp['concept_note'] = $concept_note_array ?? null;
                $temp['question_type'] =   $results->questionType->name;
                // $temp['solution_image'] =   !empty($results->solution->name) ? $results->solution->name : '-';



                $temp['question_banks_id'] = $results->question_banks_id;
                $temp['difficult_level'] = $results->difficultLevel->value;
                // $temp['questions'] = $answer->storagePath($results->question,$questionBank->id);
                // $src = asset('/storage/question/' . $questionBank->id . '/' . $results->id);
                // $temp['question']=   !empty($results->question)?
                // "<a href='".$src."' class='questionimg'
                // data-toggle='tooltip' title='".$results->question."'
                // >". $results->question."</a>":'-';

                // $temp['language'] = $results->language->name;
                $temp['tags'] = $results->tags;
                // $temp['location'] = $results->location->name;
                $temp['reviewer'] = $results->reviewer->name;
                $temp['author'] = $results->author->name;

                $topic_array = [];
                foreach ($results->topicQuestion as $topic_val) {
                    foreach ($topic_val->topic as $topic_name) {
                        $topic_array[] = $topic_name->name;
                    }
                }
                $temp['topic_name'] = $topic_array;
                // $temp['sub_topic_name'] = $results->subTopic->name;

                $temp['status'] = $results->status;
                $temp['created_at'] = $results->created_at;
                $temp['created_by'] = $results->createdBy->name;
                $question[] = (object)$temp;
                $i++;
            }

            return $question;
        }
    }
      public function solution()
{
    return $this->hasOne(Solution::class, 'question_id', 'id');
}

        
}
