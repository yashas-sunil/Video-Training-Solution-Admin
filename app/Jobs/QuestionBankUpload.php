<?php

namespace App\Jobs;

use Exception;
use App\Concept;
use App\AppAnswer;
use Carbon\Carbon;
use App\Models\Type;
use App\Models\Audio;
use App\Models\Level;
use App\Models\Topic;
use App\Models\Answer;
use App\Models\Author;
use App\Models\Course;
use App\Models\Chapter;
// use App\Models\Location;
use App\Models\Content;
use App\Models\Subject;
use App\Models\Summary;
use App\Models\Language;
use App\Models\Question;
use App\Models\Reviewer;
use App\Models\Solution;
use App\Type as AppType;
use App\Models\Paragraph;
// use App\Models\SubTopic;
use App\Models\AnswerType;
use App\Models\Importance;
use App\Models\Subchapter;
use App\Models\Championship;
use App\Models\QuestionBank;
use App\Models\QuestionType;
use App\Models\SolutionImage;
use App\Models\TopicQuestion;
use Illuminate\Bus\Queueable;
use App\Models\DifficultLevel;
use App\Models\ChaptersQuestion;
use Illuminate\Support\Facades\DB;
use App\Models\QuestionBankHistory;
use Illuminate\Support\Facades\Log;
use App\AnswerType as AppAnswerType;
use App\Audio as AppAudio;
use App\ChaptersQuestion as AppChaptersQuestion;
use App\Subchapter as AppSubchapter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\QuestionBank as AppQuestionBank;
use Illuminate\Queue\InteractsWithQueue;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\SolutionImage as AppSolutionImage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Quiz\Question as QuizQuestion;
use App\QuestionBankHistory as AppQuestionBankHistory;
use App\Solution as AppSolution;
use App\Topic as AppTopic;
use App\TopicQuestion as AppTopicQuestion;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Alignment;

class QuestionBankUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $readexcel;
    public $qbid;
    public $input;
    public $storage;
    public $storage_folder_Path;
    public $storage_dummy_folder_Path;
    public $type;
    public $insert_qb;
    public $updated_by;
    public $storage_dummy_folder_name;
    public $mailemail;
    public $history_id;
    protected $questionBankDetails;
    protected $question;
    protected $solution;
    protected $solutionImage;
    protected $concept;
    protected $championship_data;
    public function __construct($readexcel, $qbid, $input, $storage, $storage_folder_Path, $storage_dummy_folder_Path, $type, $storage_dummy_folder_name, $insert_qb, $updated_by, $mailemail, $history_id, $championship_data)
    {

        $this->readexcel = $readexcel;
        $this->qbid = $qbid;
        $this->input = $input;
        $this->storage = $storage;
        $this->storage_folder_Path = $storage_folder_Path;
        $this->storage_dummy_folder_Path = $storage_dummy_folder_Path;
        $this->type = $type;
        $this->insert_qb = $insert_qb;
        $this->updated_by = $updated_by;
        $this->storage_dummy_folder_name = $storage_dummy_folder_name ?? null;
        $this->mailemail       = $mailemail;
        $this->history_id  = $history_id;
        $this->championship_data = $championship_data;   //save championship data if qb upload initated by create qb then null
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function deleteimages($qbid, $questions)
    {
        if (!empty($questions)) {

            foreach ($questions as $question_id) {

                $storage_folder_Path = storage_path('app/public/questionbank/' . $qbid . '/' . $question_id);
                File::deleteDirectory($storage_folder_Path);
            }
        }
    }
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
Log::warning('QB DEBUG: FOREIGN KEY CHECKS DISABLED');
        DB::listen(function ($query) {
    if (str_contains(strtolower($query->sql), 'insert')) {
        Log::info('QB SQL', [
            'sql' => $query->sql,
            'bindings' => $query->bindings
        ]);
    }
});

          Log::info('QB DEBUG: start');
          Log::warning('QB INPUT DEBUG', [
    'input' => $this->input ?? null,
    'category' => $this->input['category'] ?? 'CATEGORY_MISSING',
    'championship_data' => $this->championship_data,
]);
        $obj_PhpOfficess = new Spreadsheet();
        $obj_PhpOfficess->setActiveSheetIndex(0);
        $obj_PhpOfficess->getDefaultStyle()
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $this->readexcel['obj_PhpOffice'] = $obj_PhpOfficess;
        $insert_data = array();
        $insert_chapter_question = array();
        $insert_topic_question = array();
        $result_show = array();


        $QuestionBankDetails = new AppQuestionBank();
        $question            = new QuizQuestion();
        $solution = new \App\Solution();
        $this->solution = $solution;

        $solutionImage = new AppSolutionImage();
        $this->solutionImage = $solutionImage;

        $concept = new Concept();
        $this->concept = $concept;
        $answer              = new AppAnswer();
        $qbhistory           = new AppQuestionBankHistory();
        $chapter_question = new AppChaptersQuestion();
        $topic_question = new AppTopicQuestion();
        // $paragraph = new AppParagraph();

        $this->questionBankDetails = $QuestionBankDetails;
        $this->question            = $question;

        $readexcel = $this->readexcel;
        $qbid = $this->qbid;
        $input = $this->input;
        $storage = $this->storage;
        $storage_folder_Path = $this->storage_folder_Path;
        $storage_dummy_folder_Path = $this->storage_dummy_folder_Path;
        $type = $this->type;
        $insert_qb = $this->insert_qb;
        $updated_by = $this->updated_by;
        $storage_dummy_folder_name = $this->storage_dummy_folder_name;
        $mailemails = $this->mailemail;
        $history_id = $this->history_id;

        $counter = 1;
        $check_if_error_in_document = "No";
        $questionColumnError = "No";
        $questionImageColumnError = "No";
        $correct_answerColumnError = "No";
        $difficultColumnError = "No";
        $paragraphColumnError = "No";
        $instructionColumnError = "No";
        $subjectColumnError = "No";
        $topicColumnError = "No";
        $chapterColumnError = "No";

        $answerTypeColumnError = "No";
        $header_error = "No";
        $questionNumberColumnError = "No";
        $locationColumnError = "No";
        $authorColumnError = "No";
        $reviewerColumnError = "No";
        $tagsColumnError = "No";

        $courseColumnError = "No";
        $levelColumnError = "No";
        $subjectColumnError = "No";
        $chapterColumnError = "No";
        $subChapterColumnError = "No";
        $typeColumnError = "No";
        $summaryColumnError = "No";
        $contentColumnError = "No";
        $importanceColumnError = "No";
        $sourceColumnError = "No";
        $StatusColumnError = "No";

        $questionNumberColumnError = "No";
        $questionTypeColumnError = "No";
        $questionImageColumnError = "No";

        $conceptColumnError = "No";
        $conceptNoteColumnError = "No";

        $solutionColumnError = "No";
        $solutionImageColumnError = "No";
        $includeChampionshipError = "No";           //Championship column error

        $extra_header = array();
        $extra_data_answers = array();
        $extra_option = array();

        $solution_header = array();
        $solution_option = array();

        $solution_insert_data = array();
        $concept_insert_data = array();

        $find_max_question_id = $question->search_max_question_id();
        $get_max_question_id = $find_max_question_id + 1;
        $find_max_answer_id = $answer->search_max_question_answer_id();
        $find_max_answer_id = $find_max_answer_id + 1;

        $searchMaxSolutionId = $solution->searchMaxSolutionId();
        $searchMaxSolutionId = $searchMaxSolutionId + 1;

        $searchMaxSolutionImageId = $solutionImage->searchMaxSolutionImageId();
        $searchMaxSolutionImageId = $searchMaxSolutionImageId + 1;

        $searchMaxConcept = $concept->searchMaxConcept();
        $searchMaxConceptId = $searchMaxConcept + 1;

        $find_max_question_number = $question->search_max_question_number();
        $get_max_question_question_number = $find_max_question_number + 1;

        $findMaxChapterQuestionId = $chapter_question->findMaxChapterQuestionId();
        // $findMaxTopicQuestionId = $topic_question->findMaxTopicQuestionId();

        $index = 0;
        $fetch_length = 0;
        $option_index = 0;
        $fixwidthsize = 300;
        $fixheightsize = 300;

        $solution_index = 0;
        $solution_image_index = 0;
        $keysToUnset = ['T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE'];
        $concept_index = 0;

        $chapter_id = null;
        $sheet_data = array_filter($readexcel['sheet_data']);
        try {
            foreach ($sheet_data as $sheet_data_loop) {
                if ($counter != 1) {
                    $question_id = $get_max_question_id++;
                    $question_number = $get_max_question_question_number++;
                    $insert_data[$index]['id'] = $question_id;
                    $insert_data[$index]['question_number'] = $question_number;

                    $chapter_question_id = ++$findMaxChapterQuestionId;
                    $insert_chapter_question[$index]['id'] = $chapter_question_id;
                    $insert_chapter_question[$index]['question_id'] = $question_id;

                    $solutionId = $searchMaxSolutionId++;
                    if (!empty($qbid)) {
                        $insert_data[$index]['question_banks_id'] = $qbid;
                    }

                    try {
                        $message = $sheet_data_loop['A'];
                        if (!empty($sheet_data_loop['A'])) {
                            $course = new Course();
                            $result = $course->courseByName(trim($sheet_data_loop['A']));
                            if ($result == 0) {
                                $message = $sheet_data_loop['A'] . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('A' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $courseColumnError = "Yes";
                            } else {
                                $insert_data[$index]['course_id'] = $result;
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('A' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $courseColumnError = "Yes";
                        }

                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('A' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column A error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['B'];
                        if (!empty($sheet_data_loop['B'])) {
                            $level = new Level();
                            $result = $level->levelByName(trim($sheet_data_loop['A']), trim($sheet_data_loop['B']));
                            if ($result == 0) {
                                $message = $sheet_data_loop["B"] . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('B' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $levelColumnError = "Yes";
                            } else {
                                $insert_data[$index]['level_id'] = $result;
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('B' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $levelColumnError = "Yes";
                        }

                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('B' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column B error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['C'];
                        if (!empty($sheet_data_loop['C'])) {
                            $subject = new Subject();
                            $result = $subject->subjectByCourseName(trim($sheet_data_loop['A']), trim($sheet_data_loop['B']), trim($sheet_data_loop['C']));
                            if ($result == 0) {
                                $message = $sheet_data_loop["C"] . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('C' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $subjectColumnError = "Yes";
                            } else {
                                $insert_data[$index]['subject_id'] = $result;
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('C' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $subjectColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('C' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column C error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['D'];
                        if (!empty($sheet_data_loop['D'])) {
                            $chapter = new Chapter();
                            $result = $chapter->chapterByName(trim($sheet_data_loop['C']), trim($sheet_data_loop['D']));   //check if chapter present in database if present (and qb genration for championship check if subject selected in championship match the subject in excel (should not contain different subjects))
                            if (isset($input['chapters']) && $result != 0) {
                                if (in_array($result, $input['chapters'])) {
                                    $message = $sheet_data_loop['D'];
                                    $insert_data[$index]['chapter_id'] = $result;
                                    $insert_chapter_question[$index]['chapter_id'] = $result;
                                    $chapter_id = $result;
                                } else {
                                    $message = $sheet_data_loop["D"] . "(Select chapters, selected during championship creation)";
                                    $check_if_error_in_document = "Yes";
                                    $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('D' . $counter)->applyFromArray($readexcel['style_array_error']);
                                    $chapterColumnError = "Yes";
                                }
                            } else {
                                if ($result == 0) {
                                    $message = $sheet_data_loop["D"] . "(Not Valid)";
                                    $check_if_error_in_document = "Yes";
                                    $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('D' . $counter)->applyFromArray($readexcel['style_array_error']);
                                    $chapterColumnError = "Yes";
                                } else {
                                    // $summary_id = $chapter->summaryByChapterId($result);
                                    // if ($summary_id == 0) {
                                    //     $message = $sheet_data_loop["D"] . "(Summary Not Found)";
                                    //     $check_if_error_in_document = "Yes";
                                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('D' . $counter)->applyFromArray($readexcel['style_array_error']);
                                    //     $chapterColumnError = "Yes";
                                    // } else {
                                        $message = $sheet_data_loop['D'];
                                        $insert_data[$index]['chapter_id'] = $result;
                                        // $insert_data[$index]['summary_id'] = $summary_id;
                                        $insert_chapter_question[$index]['chapter_id'] = $result;
                                        $chapter_id = $result;
                                    // }
                                }
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('D' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $chapterColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('D' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column D error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['E'];
                        if (!empty($sheet_data_loop['E'])) {
                            $sub_chapter = new AppSubchapter();
                            $result = $sub_chapter->subChapterByName(trim($sheet_data_loop['D']), trim($sheet_data_loop['E']));
                            if ($result == 0) {
                                $message = $sheet_data_loop['E'] . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('E' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $subChapterColumnError = "Yes";
                            } else {
                                $insert_data[$index]['subchapter_id'] = $result;
                            }
                        } else {
                            $insert_data[$index]['subchapter_id'] = null;
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('E' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column E error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['F'];
                        if (!empty($sheet_data_loop['F'])) {
                            $topics = explode(',', $sheet_data_loop['F']);
                            $topicIds = [];
                            $topicNames = [];
                            $invalidTopics = [];
                            $topicColumnError = "No";

                            foreach ($topics as $topicName) {
                                $topicName = trim($topicName);
                                $topic = new AppTopic();
                                $result = $topic->topicByChapterNameCopy(trim($sheet_data_loop['D']), $topicName);

                                if ($result == 0) {
                                    $invalidTopics[] = $topicName . " (Not Valid)";
                                    $check_if_error_in_document = "Yes";
                                    $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('F' . $counter)->applyFromArray($readexcel['style_array_error']);
                                    $topicColumnError = "Yes";
                                } else {
                                    $topicIds[] = $result;
                                    $topicNames[] = $topicName;
                                    // $contentIds = $topic->contentByTopicId($topicIds);
                                    // if ($contentIds == 0) {
                                    //     $message = $sheet_data_loop['F'] . "(Content Not Found)";
                                    //     $check_if_error_in_document = "Yes";
                                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('F' . $counter)->applyFromArray($readexcel['style_array_error']);
                                    //     $chapterColumnError = "Yes";
                                    // }
                                }
                            }

                            if (!empty($invalidTopics)) {
                                $message = implode(', ', $invalidTopics);
                            } else {
                                $message = implode(', ', $topics);
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('F' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $topicColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('F' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column F error" . $e->getMessage());
                    }

                    // try {
                    //     $message = $sheet_data_loop['G'];
                    //     if (isset($sheet_data_loop['G'])) {
                    //         $concepts = explode(',', $sheet_data_loop['G']);
                    //         $conceptIds = [];
                    //         $invalidConcepts = [];
                    //         $conceptColumnError = "No";
                    //         foreach ($concepts as $key => $conceptName) {
                    //             $conceptName = trim($conceptName);
                    //             $concept = new Concept();
                    //             // $result = $concept->conceptByTopicId($topicIds, $conceptName);
                    //             // if ($result == 0) {
                    //             //     $invalidConcepts[] = $conceptName . " (Not Valid)";
                    //             //     $check_if_error_in_document = "Yes";
                    //             //     $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('G' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //             //     $conceptColumnError = "Yes";
                    //             // } else {
                    //             //     $conceptIds[] = $result;
                    //             // }
                    //         }

                    //         if (!empty($invalidConcepts)) {
                    //             $message = implode(', ', $invalidConcepts);
                    //         } else {
                    //             $message = implode(', ', $concepts);
                    //             foreach ($conceptIds as $concept_id) {
                    //                 $maxConceptId = $searchMaxConceptId++;
                    //                 $concept_insert_data[] = [
                    //                     'id' => $maxConceptId,
                    //                     'questions_id' => $question_id,
                    //                     'concept_id' => $concept_id,
                    //                     'concept_note' => !empty($sheet_data_loop['U']) ? trim($sheet_data_loop['U']) : null,
                    //                     'status' => Concept::ACTIVE,
                    //                     'created_by' => $updated_by,
                    //                     'updated_by' => $updated_by,
                    //                     'created_at' => Carbon::now(),
                    //                     'updated_at' => Carbon::now(),
                    //                 ];
                    //             }
                    //         }
                    //     } else {
                    //         $audio_id = AppAudio::whereIn('topic_id', $topicIds)
                    //             // ->where('status', Audio::ACTIVE)
                    //             ->get();

                    //         foreach ($audio_id as $audio) {
                    //             $maxConceptId = $searchMaxConceptId++;
                    //             $concept_insert_data[] = [
                    //                 'id' => $maxConceptId,
                    //                 'questions_id' => $question_id,
                    //                 'concept_id' => $audio->id,
                    //                 'concept_note' => !empty($sheet_data_loop['U']) ? trim($sheet_data_loop['U']) : null,
                    //                 'status' => Concept::ACTIVE,
                    //                 'created_by' => $updated_by,
                    //                 'updated_by' => $updated_by,
                    //                 'created_at' => Carbon::now(),
                    //                 'updated_at' => Carbon::now(),
                    //             ];
                    //         }
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('G' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column G error" . $e->getMessage());
                    // }

                    try {
                        $message = $sheet_data_loop['H'];
                        if (isset($sheet_data_loop['H'])) {
                            $message = $sheet_data_loop['H'];
                            $difficult = new \App\DifficultLevel();
                            $difficults = $difficult->difficultByName(trim($sheet_data_loop['H']));
                            if ($difficults == 0) {
                                $message = $sheet_data_loop["H"] . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('H' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $difficultColumnError = "Yes";
                            } else {
                                $insert_data[$index]['difficult_levels_id'] = $difficults;
                            }
                        } else {
                            $message = 'Mandatory';
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('H' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $difficultColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('H' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column H error" . $e->getMessage());
                    }

                    // try {
                    //     $message = $sheet_data_loop['I'];
                    //     if (!empty($sheet_data_loop['I'])) {
                    //         $importance = new Importance();
                    //         $result = $importance->importanceByName(trim($sheet_data_loop['I']));
                    //         if ($result == 0) {
                    //             $message = $sheet_data_loop['I'] . "(Not Valid)";
                    //             $check_if_error_in_document = "Yes";
                    //             $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('I' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //             $courseColumnError = "Yes";
                    //         } else {
                    //             $insert_data[$index]['importance_id'] = $result;
                    //         }
                    //     } else {
                    //         $message = "Mandatory";
                    //         $check_if_error_in_document = "Yes";
                    //         $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('I' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //         $importanceColumnError = "Yes";
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('I' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column I error" . $e->getMessage());
                    // }

                    try {
                        $message = $sheet_data_loop['J'];
                        if (!empty($sheet_data_loop['J'])) {
                            $questionType = new \App\QuestionType();
                            $result = $questionType->questionTypeByName(trim($sheet_data_loop['J']));
                            if ($result == 0) {
                                $message = $sheet_data_loop['J'] . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('J' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $courseColumnError = "Yes";
                            } else {
                                $insert_data[$index]['question_types_id'] = $result;
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('J' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $questionTypeColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('J' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column J error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['K'];
                        if (!empty($sheet_data_loop['K'])) {
                            $insert_data[$index]['source'] = $sheet_data_loop['K'];
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('K' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $sourceColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('K' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column K error" . $e->getMessage());
                    }

                    // L Column Logic Pending(QuestionID)

                    try {
                        $message = $sheet_data_loop['M'];
                        $ques_type = null;
                        if (!empty($sheet_data_loop['M'])) {
                            $message = $sheet_data_loop['M'];

                            $types = new AppType();
                            $ques_type = $types->typeByName(trim(strtolower($sheet_data_loop['M'])));

                            if ($ques_type == 0) {
                                $message = $sheet_data_loop['M'] . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('M' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $typeColumnError = "Yes";
                            } else {
                                $insert_data[$index]['types_id'] = $ques_type;
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('M' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $typeColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('M' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column M error" . $e->getMessage());
                    }


                    try {
                        $message = trim($sheet_data_loop['T']);
                        $valid_answer = !empty($message) && (is_numeric($message) || $sheet_data_loop['N'] == "multiple");

                        if ($valid_answer) {
                            $insert_data[$index]['correct_answer'] = $message;
                            $myAnswerArray = explode(',', $message);
                        } else {
                            $message = " (Not Valid)";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('T' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $correct_answerColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('T' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column T error" . $e->getMessage());
                    }

                    try {
                        $message = "";
                        $message_answer = "";
                        // $myAnswerArray = [];  //dont uncomment
                        $answerType = null;
                        if (!empty($sheet_data_loop['N'])) {
                            $message = $sheet_data_loop['N'];
                            $message_answer = trim($sheet_data_loop['T']);
                            $answerTypes = new AppAnswerType();
                            $answerType = $answerTypes->answerTypeByName(trim(strtolower($sheet_data_loop['N'])));

                            if ($answerType == 0) {
                                $message = $sheet_data_loop['N'] . "(Not Valid)";
                                $message_answer = trim($sheet_data_loop['T']);
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('N' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $answerTypeColumnError = "Yes";
                            } else {
                                if (count($myAnswerArray) == 1) {
                                    if (!empty($answerType)) {
                                        $insert_data[$index]['answer_types_id'] = $answerType;
                                    } else {
                                        $message = $sheet_data_loop["Q"] . "(Not Valid)";
                                        $check_if_error_in_document = "Yes";
                                        $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('N' . $counter)->applyFromArray($readexcel['style_array_error']);
                                        $answerTypeColumnError = "Yes";


                                        $message_answer = $sheet_data_loop["P"] . "(Not Valid)";
                                        $check_if_error_in_document = "Yes";
                                        $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('T' . $counter)->applyFromArray($readexcel['style_array_error']);
                                        $correct_answerColumnError = "Yes";
                                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('T' . $counter, $message_answer);
                                    }
                                } else {
                                    $insert_data[$index]['answer_types_id'] = $answerType;
                                }
                            }
                        } else {
                            $message = "Mandatory";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('N' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $answerTypeColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('N' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column N error" . $e->getMessage());
                    }

                    try {
                        $option_data = array_slice($sheet_data_loop, 15, abs($fetch_length));
                        foreach ($keysToUnset as $key) {
                            unset($option_data[$key]);
                        }
                        $inc = 0;
                        $empty_inc = 0;
                        $option_index_inc = 1;
                        $answer_image = '';
                        $message = '';
                        foreach ($option_data as $option_key => $option_value) {
                            if ($option_value == '' || is_null($option_value)) {
                                $empty_inc++;
                            }

                            if (empty($option_data['P']) && $option_key == 'P') {
                                $message = "Question Answer not added";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle($option_key . $counter)->applyFromArray($readexcel['style_array_error']);
                                $header_error = "Yes";
                            }

                            if (!empty($option_data[$option_key]) && $option_data[$option_key] != null) {
                                $answer_id = $find_max_answer_id++;
                                $extra_option[$option_index]['question_id'] = $question_id;
                                $extra_option[$option_index]['id'] = $answer_id;
                                $extra_option[$option_index]['answer'] = $option_value;
                                $extra_option[$option_index]['answer_types_id'] = $answerType;
                                $extra_option[$option_index]['created_by'] = $updated_by;
                                $extra_option[$option_index]['updated_by'] = $updated_by;
                                $extra_option[$option_index]['created_at'] = Carbon::now();
                                $extra_option[$option_index]['updated_at'] = Carbon::now();

                                if (!empty($option_index_inc) && !empty($myAnswerArray)) {
                                    if (in_array($option_index_inc, $myAnswerArray) != false) {
                                        $extra_option[$option_index]['correctans'] = 1;
                                    } else {
                                        $extra_option[$option_index]['correctans'] = 0;
                                    }
                                }

                                preg_match_all('/<<(.*?)>>/', $option_data[$option_key], $matches);
                                $answer_image = $matches[1] ?? [];
                                $answer_image_count = count($answer_image);
                                $answer_image_index = 0;
                                $answer_flag = false;
                                $ans_image = null;
                                foreach ($answer_image as $file_key => $file) {
                                    $answer_image_dummy_path = 'questionbank/upload/' . $storage_dummy_folder_name . '/' . $file;
                                    $file_exists = Storage::disk('public')->exists($answer_image_dummy_path);
                                    $fileextension = explode(".", $file);
                                    if ($file_exists == true && isset($fileextension[1])) {
                                        $imagePath = storage_path('app/public') . '/' . $answer_image_dummy_path;

                                        // $resizedImage = Image::make($imagePath)->resize(300, 300)->encode($fileextension[1]);

                                        $answer_image_storge_path = 'questionbank/' . $qbid . '/' . $question_id . '/' . $answer_id . '/' . $answer_id . '_' . $answer_image_index . '.' . $fileextension[1];

                                        // Storage::disk('public')->put($answer_image_storge_path, $resizedImage);

                                        $filename = basename($file);
                                        $extra_option[$option_index]['answer'] = str_replace('<' . $filename . '>', 'img src="' . $answer_image_storge_path . '"', $extra_option[$option_index]['answer']);
                                        $ans_image = $ans_image ? $ans_image . ',' . $answer_image_storge_path : $answer_image_storge_path;
                                    } else {
                                        $answer_flag = true;
                                    }
                                    $answer_image_index++;
                                }

                                $extra_option[$option_index]['option_image'] = $ans_image;

                                if ($answer_flag == true) {
                                    $message = "Images Not Found";
                                    $check_if_error_in_document = "Yes";
                                    $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle($option_key . $counter)->applyFromArray($readexcel['style_array_error']);
                                    $header_error = "Yes";
                                }
                            }

                            $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue($option_key . $counter, $option_value);

                            if ($empty_inc > 1) {
                                unset($extra_option[$option_index]);
                            }
                            $empty_inc = 0;
                            $option_index++;
                            $option_index_inc++;
                        }
                        $extra_data = array();
                    } catch (\Exception $e) {
                        Log::info("Option Column error: " . $e->getMessage());
                    }

                    // try {
                    //     $message = $sheet_data_loop['O'];
                    //     if (!empty($sheet_data_loop['O'])) {
                    //         $insert_data[$index]['question'] = $sheet_data_loop['O'];
                    //         $question_image_count_array = [];
                    //         preg_match_all('/<<(.*?)>>/', $message, $matches);
                    //         $question_image = $matches[1] ?? [];
                    //         $question_image_count = count($question_image);
                    //         $question_image_index = 0;
                    //         $question_flag = false;
                    //         $ques_image = null;
                    //         foreach ($question_image as $file_key => $file) {
                    //             $question_image_dummy_path = 'questionbank/upload/' . $storage_dummy_folder_name . '/' . $file;
                    //             $file_exists = Storage::disk('public')->exists($question_image_dummy_path);
                    //             $fileextension = explode(".", $file);
                    //             if ($file_exists == true && isset($fileextension[1])) {
                    //                 $imagePath = storage_path('app/public') . '/' . $question_image_dummy_path;

                    //                 $resizedImage = Image::make($imagePath)->resize(300, 300)->encode($fileextension[1]);

                    //                 $question_image_storge_path = 'questionbank/' . $qbid . '/' . $question_id . '/' . $question_id . '_' . $question_image_index . '.' . $fileextension[1];

                    //                 Storage::disk('public')->put($question_image_storge_path, $resizedImage);

                    //                 $filename = basename($file);
                    //                 $insert_data[$index]['question'] = str_replace('<' . $filename . '>', 'img src="' . $question_image_storge_path . '"', $insert_data[$index]['question']);
                    //                 $ques_image = $ques_image ? $ques_image . ',' . $question_image_storge_path : $question_image_storge_path;
                    //             } else {
                    //                 $question_flag = true;
                    //             }
                    //             $question_image_index++;
                    //         }

                    //         if ($question_flag == true) {
                    //             $message = "Images Not Found";
                    //             $check_if_error_in_document = "Yes";
                    //             $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('O' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //             $questionColumnError = "Yes";
                    //         }
                    //         $insert_data[$index]['image'] = $ques_image;
                    //     } else {
                    //         $message = "Mandatory";
                    //         $check_if_error_in_document = "Yes";
                    //         $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('O' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //         $questionColumnError = "Yes";
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('O' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column O error: " . $e->getMessage());
                    // }

                    // try {
                    //     $message = $sheet_data_loop['V'];
                    //     if (!empty($sheet_data_loop['V'])) {
                    //         //Insert Solution Data
                    //         $solution_insert_data[$index]['id'] = $solutionId;
                    //         $solution_insert_data[$index]['questions_id'] = $question_id;
                    //         $solution_insert_data[$index]['name'] = $sheet_data_loop['V'];
                    //         $solution_insert_data[$index]['status'] = AppSolution::ACTIVE;
                    //         $solution_insert_data[$index]['created_at'] = Carbon::now();
                    //         $solution_insert_data[$index]['updated_at'] = Carbon::now();

                    //         preg_match_all('/<<(.*?)>>/', $message, $matches);
                    //         $solution_image = $matches[1] ?? [];
                    //         $solution_image_count = count($solution_image);
                    //         $solution_image_index = 0;
                    //         $solution_flag = false;

                    //         foreach ($solution_image as $file_key => $file) {
                    //             $solution_image_dummy_path = 'questionbank/upload/' . $storage_dummy_folder_name . '/' . $file;
                    //             $file_exists = Storage::disk('public')->exists($solution_image_dummy_path);
                    //             $fileextension = explode(".", $file);
                    //             if ($file_exists == true && isset($fileextension[1])) {
                    //                 $imagePath = storage_path('app/public') . '/' . $solution_image_dummy_path;

                    //                 $resizedImage = Image::make($imagePath)->resize(300, 300)->encode($fileextension[1]);

                    //                 $solution_image_storge_path = 'questionbank/' . $qbid . '/' . $solutionId . '/' . $solutionId . '_' . $solution_image_index . '.' . $fileextension[1];

                    //                 Storage::disk('public')->put($solution_image_storge_path, $resizedImage);

                    //                 $filename = basename($file);
                    //                 $solution_insert_data[$index]['name'] = str_replace('<' . $filename . '>', 'img  src="' . $solution_image_storge_path . '"', $solution_insert_data[$index]['name']);

                    //                 $solutionImageId = $searchMaxSolutionImageId++;
                    //                 $solution_option[$solution_index]['id'] = $solutionImageId;
                    //                 $solution_option[$solution_index]['solution_id'] = $solutionId;
                    //                 $solution_option[$solution_index]['name'] = trim($sheet_data_loop['V']);
                    //                 $solution_option[$solution_index]['path'] =  $solution_image_storge_path;
                    //                 $solution_option[$solution_index]['created_at'] = Carbon::now();
                    //                 $solution_option[$solution_index]['updated_at'] = Carbon::now();
                    //             } else {
                    //                 $solution_flag = true;
                    //             }
                    //             $solution_index++;
                    //             $solution_image_index++;
                    //         }

                    //         if ($solution_flag == true) {
                    //             $message = "Images Not Found";
                    //             $check_if_error_in_document = "Yes";
                    //             $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('V' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //             $solutionImageColumnError = "Yes";
                    //         }
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('V' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column V error" . $e->getMessage());
                    // }

                    try {
                        $message = $sheet_data_loop['W'];
                        if (isset($sheet_data_loop['W']) && is_numeric($sheet_data_loop['W'])) {
                            $insert_data[$index]['marks'] = trim($sheet_data_loop['W']);
                        } else {
                            $message = $sheet_data_loop['W'] . "(Not Valid)";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('W' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $sourceColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('W' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column W error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['X'];
                        if (isset($sheet_data_loop['X']) && is_numeric($sheet_data_loop['X'])) {
                            $insert_data[$index]['negative_marks'] = $sheet_data_loop['X'];
                        } else {
                            $message = $sheet_data_loop['X'] . "(Not Valid)";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('X' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $sourceColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('X' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column X error" . $e->getMessage());
                    }

                    try {
                        $message = $sheet_data_loop['Y'];
                        if (isset($sheet_data_loop['Y']) && is_numeric($sheet_data_loop['Y'])) {
                            $message = $sheet_data_loop['Y'];

                            $insert_data[$index]['time'] = trim($sheet_data_loop['Y']);
                        } else {
                            $message .= $sheet_data_loop['Y'] . "(Not Valid)";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('Y' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $sourceColumnError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('Y' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column Y error" . $e->getMessage());
                    }

                    // try {
                    //     $message = $sheet_data_loop['Z'];
                    //     if (isset($sheet_data_loop['Z'])) {
                    //         $author = new Author();
                    //         $authors = $author->authorByName(trim(strtolower($sheet_data_loop['Z'])));
                    //         if ($authors == 0) {
                    //             $message = $sheet_data_loop["Z"] . "(Not Valid)";
                    //             $check_if_error_in_document = "Yes";
                    //             $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('Z' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //             $authorColumnError = "Yes";
                    //         } else {
                    //             $insert_data[$index]['authors_id'] = $authors;
                    //         }
                    //     } else {
                    //         $message = 'Mandatory';
                    //         $check_if_error_in_document = "Yes";
                    //         $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('Z' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //         $authorColumnError = "Yes";
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('Z' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column Z error" . $e->getMessage());
                    // }

                    // try {
                    //     $message = $sheet_data_loop['AA'];
                    //     if (isset($sheet_data_loop['AA'])) {
                    //         $reviewer = new Reviewer();
                    //         $reviewers = $reviewer->reviewerByName(trim(strtolower($sheet_data_loop['AA'])));
                    //         if ($reviewers == 0) {
                    //             $message = $sheet_data_loop["AA"] . "(Not Valid)";
                    //             $check_if_error_in_document = "Yes";
                    //             $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AA' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //             $reviewerColumnError = "Yes";
                    //         } else {
                    //             $insert_data[$index]['reviewers_id'] = $reviewers;
                    //         }
                    //     } else {
                    //         $message = 'Mandatory';
                    //         $check_if_error_in_document = "Yes";
                    //         $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AA' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //         $reviewerColumnError = "Yes";
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('AA' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column AA error" . $e->getMessage());
                    // }

                    try {
                        $message = $sheet_data_loop['AB'];
                        if (isset($sheet_data_loop['AB'])) {
                            $value = trim($sheet_data_loop['AB']);
                            if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                                $status = (int)$value;
                                if ($status === 1 || $status === 0) {
                                    $insert_data[$index]['status'] = $status;
                                    $insert_chapter_question[$index]['status'] = $status;
                                    // Topic Question Store
                                    foreach ($topicIds as $key => $topicId) {
                                        $topic_question_id = ++$findMaxTopicQuestionId;
                                        if (!empty($contentIds)) {
                                            $contentId = $contentIds[$key];
                                        }
                                        $insert_topic_question[] = [
                                            'id' => $topic_question_id,
                                            // 'questions_id' => $question_id,
                                            // 'topics_id' => $topicId,
                                            // 'content_id' => $contentId ?? null,
                                            // 'status' => $status,
                                        ];
                                    }
                                    $message = $status;
                                } else {
                                    $message = $status . " (Not Valid)";
                                    $check_if_error_in_document = "Yes";
                                    $StatusColumnError = "Yes";
                                    $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AB' . $counter)->applyFromArray($readexcel['style_array_error']);
                                }
                            } else {
                                $message = $value . " (Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $StatusColumnError = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AB' . $counter)->applyFromArray($readexcel['style_array_error']);
                            }
                        } else {
                            $message = " (Not Valid)";
                            $check_if_error_in_document = "Yes";
                            $StatusColumnError = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AB' . $counter)->applyFromArray($readexcel['style_array_error']);
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->setCellValue('AB' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column AB error" . $e->getMessage());
                    }

                    // try {
                    //     $message = $sheet_data_loop['AC'];
                    //     if (isset($sheet_data_loop['AC'])) {
                    //         $tags = trim($sheet_data_loop['AC']);
                    //         if (!empty($tags)) {
                    //             $tagFlag = $this->question->checkTags($tags);
                    //             if (!empty($tagFlag)) {
                    //                 $insert_data[$index]['tags'] = $tags;
                    //             } else {
                    //                 $message = 'Tag does not exist in master';
                    //                 $check_if_error_in_document = "Yes";
                    //                 $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AC' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //                 $tagsColumnError = "Yes";
                    //             }
                    //         }
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('AC' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column AC error" . $e->getMessage());
                    // }

                    try {
                        $message = $sheet_data_loop['AD'];
                        if (isset($sheet_data_loop['AD']) && is_numeric($sheet_data_loop['AD'])) //Check if championship column has data if it has, check if it is either 0 or 1
                        {
                            if ((int) trim($sheet_data_loop['AD']) == 1 || (int) trim($sheet_data_loop['AD']) == 0) {
                                $insert_data[$index]['include_championship'] = (int)trim($sheet_data_loop['AD']);
                            } else {
                                $message = $sheet_data_loop['AD'] . "(Data will be either one or zero)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AD' . $counter)->applyFromArray($readexcel['style_array_error']);
                                $includeChampionshipError = "Yes";
                            }
                        } else {
                            $message = $sheet_data_loop['AD'] . "(Data will be either one or zero)";
                            $check_if_error_in_document = "Yes";
                            $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AD' . $counter)->applyFromArray($readexcel['style_array_error']);
                            $includeChampionshipError = "Yes";
                        }
                        $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('AD' . $counter, $message);
                    } catch (\Exception $e) {
                        Log::info("Column AD error" . $e->getMessage());
                    }

                    // try {
                    //     $message = $sheet_data_loop['AE'];
                    //     if ($ques_type == 1 && empty($sheet_data_loop['AE'])) {
                    //         $message = "(Not Valid)";
                    //         $check_if_error_in_document = "Yes";
                    //         $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AE' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //         $paragraphColumnError = "Yes";
                    //     } elseif ($ques_type == 2 && !empty($sheet_data_loop['AE'])) {
                    //         $message = $sheet_data_loop['AE'] . "(Not Valid)";
                    //         $check_if_error_in_document = "Yes";
                    //         $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AE' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //         $paragraphColumnError = "Yes";
                    //     } elseif ($ques_type == 1 && !empty($sheet_data_loop['AE'])) {
                    //         if (env("PARAGRAPH") == true) {
                    //             $paragraphs = Paragraph::where('chapters_id', $chapter_id)
                    //                 ->where('paragraph', $sheet_data_loop['AE'])
                    //                 ->first();

                    //             if (empty($paragraphs)) {
                    //                 $paragraph = new Paragraph;
                    //                 $paragraph->chapters_id = $chapter_id;
                    //                 $paragraph->paragraph = $sheet_data_loop['AE'];
                    //                 $paragraph->status = Paragraph::ACTIVE;
                    //                 $paragraph->created_by = $updated_by;
                    //                 $paragraph->updated_by = $updated_by;
                    //                 $paragraph->save();
                    //             }
                    //             $result = $paragraph->paragraphByChapterName(trim($sheet_data_loop['D']), trim($sheet_data_loop['AE']));

                    //             if ($result == 0) {
                    //                 $message = $sheet_data_loop["AE"] . "(Not Valid)";
                    //                 $check_if_error_in_document = "Yes";
                    //                 $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AL' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //                 $subjectColumnError = "Yes";
                    //             } else {
                    //                 $insert_data[$index]['paragraph_id'] = $result;
                    //             }
                    //         } else {
                    //             $result = $paragraph->paragraphByChapterName(trim($sheet_data_loop['D']), trim($sheet_data_loop['AL']));
                    //             if ($result == 0) {
                    //                 $message = $sheet_data_loop["AE"] . "(Not Valid)";
                    //                 $check_if_error_in_document = "Yes";
                    //                 $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle('AE' . $counter)->applyFromArray($readexcel['style_array_error']);
                    //                 $subjectColumnError = "Yes";
                    //             } else {
                    //                 $insert_data[$index]['paragraph_id'] = $result;
                    //             }
                    //         }
                    //     } else {
                    //         $insert_data[$index]['paragraph_id'] = null;
                    //     }
                    //     $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue('AE' . $counter, $message);
                    // } catch (\Exception $e) {
                    //     Log::info("Column AE error" . $e->getMessage());
                    // }

                    $insert_data[$index]['updated_by'] = $updated_by;
                    $insert_data[$index]['created_by'] = $updated_by;
                    $insert_chapter_question[$index]['created_at'] = Carbon::now();
                    $insert_chapter_question[$index]['updated_at'] = Carbon::now();
                    $insert_data[$index]['created_at'] = Carbon::now();
                    $insert_data[$index]['updated_at'] = Carbon::now();

                    $index++;
                } else {
                    $data = array_slice($sheet_data_loop, 26);
                    foreach ($keysToUnset as $key) {
                        unset($data[$key]);
                    }
                    $inc = 0;
                    $prev_header = '';
                    $extra_header = array_filter($data);
                    $header_errors = array();
                    $option_inc = 1;

                    foreach ($extra_header as $extra_header_key => $extra_header_value) {



                        if (($inc % 2) == 0 && ($inc != 8)) {    //dont include championship column

                            $prev_header = $extra_header_value;

                            $message = "";
                            if (strpos(strtolower($extra_header_value), 'Image') !== false || strtolower($extra_header_value) !== 'option' . $option_inc) {

                                $header_errors[] = array($extra_header_key . '1', $extra_header_value, $extra_header_key, $header_error);
                                $message = $extra_header_value . "(Not Valid)";
                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle($extra_header_key . $counter)->applyFromArray($readexcel['style_array_error']);
                                $header_error = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue($extra_header_key . $counter, $message);
                            } else {
                                $header_errors[] = array($extra_header_key . '1', $extra_header_value, $extra_header_key, '');
                            }
                            $option_inc++;
                        } else {
                            $message = "";
                            if ($extra_header_value !== $prev_header . '_Image' && ($inc != 8)) {   //dont include championship column

                                $header_errors[] = array($extra_header_key . '1', $extra_header_value, $extra_header_key, $header_error);
                                $message = $extra_header_value . "(Not Valid)";

                                $check_if_error_in_document = "Yes";
                                $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle($extra_header_key . $counter)->applyFromArray($readexcel['style_array_error']);
                                $header_error = "Yes";

                                $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue($extra_header_key . $counter, $message);
                            } else {
                                $header_errors[] = array($extra_header_key . '1', $extra_header_value, $extra_header_key, '');
                            }
                        }

                        $inc++;
                    }

                    $header_data = array_filter($sheet_data_loop);
                    $last_index = count($header_data);

                    $fetch_length = $last_index - 15;
                }
                $myAnswerArray = '';
                $counter++;
            }
        } catch (\Exception $e) {
            dd($e);
            Log::info("Qb Upload Issue" . $e->getMessage());
        }

        $ErrorMessageDisplay = "";


        if ($check_if_error_in_document == "Yes") //Store Excel in Directory if error is found
        {
            $columns = array(
                array("A1", "Course", "A", $courseColumnError),
                array("B1", "Level", "B", $levelColumnError),
                array("C1", "Subject", "C", $subjectColumnError),
                array("D1", "Chapter", "D", $chapterColumnError),
                array("E1", "Subchapter", "E", $subChapterColumnError),
                array("F1", "Topic", "F", $topicColumnError),
                array("G1", "Concept", "G", $conceptColumnError),
                array("H1", "Difficulty Level", "H", $difficultColumnError),
                array("I1", "Importance", "I", $importanceColumnError),
                array("J1", "Type", "J", $questionTypeColumnError),
                array("K1", "Source", "K", $sourceColumnError),
                array("L1", "QuestionID", "L", ""),
                array("M1", "Case", "M", $typeColumnError),
                array("N1", "QuestionType", "N", $answerTypeColumnError),
                array("O1", "Question", "O", $questionColumnError),
                array("T1", "Right Option", "T", $correct_answerColumnError),
                array("U1", "Concept Note", "U", $conceptNoteColumnError),
                array("V1", "Solution", "V", $solutionColumnError),
                array("W1", "Marks", "W", ""),
                array("X1", "Negative Marks", "X", ""),
                array("Y1", "Time", "Y", ""),
                array("Z1", "Author", "Z", $authorColumnError),
                array("AA1", "Reviewer", "AA", $reviewerColumnError),
                array("AB1", "Status", "AB", $StatusColumnError),
                array("AC1", "Tags", "AC", $tagsColumnError),
                array("AD1", "Championship", "AD", $includeChampionshipError),   //add championship qb upload error
                array("AE1", "Paragraph", "AE", $paragraphColumnError),
            );
            $columns = array_merge($columns, $header_errors);


            try {
                foreach ($columns as $columns_loop) {

                    $readexcel['obj_PhpOffice']->getActiveSheet()->SetCellValue($columns_loop[0], $columns_loop[1]);
                    if ($columns_loop[3] == "Yes") {
                        $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle($columns_loop[0])->applyFromArray($readexcel['error_column_style_array']);
                    } else if ($columns_loop[3] == "No") {
                        $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle($columns_loop[0])->applyFromArray($readexcel['style_array']);
                    } else {
                        $readexcel['obj_PhpOffice']->getActiveSheet()->getStyle($columns_loop[0])->applyFromArray($readexcel['style_array']);
                    }
                    $readexcel['obj_PhpOffice']->getActiveSheet()->getColumnDimension($columns_loop["2"])->setAutoSize(true);
                }
            } catch (\Throwable $th) {
                dd($th);
                Log::info("Columns Error" . $th->getMessage());
            }

            $objWriter = IOFactory::createWriter($readexcel['obj_PhpOffice'], 'Xlsx');

            $questionbank_Error_name = 'Documents/question_errors' . time() . '.xlsx';
            $question_Error_store_location = base_path('public/' . $questionbank_Error_name);


            $objWriter->save($question_Error_store_location);
            if ($type == 1) {
                //check question bank history
                Log::info('foreignkey');
                \App\QuestionBankHistory::where('question_banks_id', $qbid)->delete();
                $delete = DB::table('question_banks')->where('id', $qbid)->delete();



                $storage_folder_Path = storage_path('app/public/questionbank/' . $qbid);

                File::deleteDirectory($storage_folder_Path);
            } else {

                $delete_question_id = array_column($insert_data, 'id');
                $this->deleteimages($qbid, $delete_question_id);
            }
            $history = $qbhistory->updateQuestionBankHistory($input, $qbid, 3, $history_id, $filepath = $storage_dummy_folder_name, $message = "File are not uploaded because we found errors in the file. Error are highlighted in yellow inside the Excel Sheet.");

            if ($this->championship_data != null) {         //if championship qb failed to upload due to some errors send mail

                $result_show['message'] = "<p align=\"left\" style=\"background-color: lightgrey\">"
                    . "QuestionBank Inserted : 0 <br/>"
                    . "Championship not created due to incorrect question blank data. To view the errors, <a href=\"" . url('/') . "/" . $questionbank_Error_name . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Download Panel Error Excel Sheet\" download class=\"btn btn-link\" style=\"color: red;\">Click here</a>"
                    . "</p>";
                $result_show['result'] = 0;
            } else {                                      //question bank initiated via create QB
                $result_show['message'] = "<p align=\"left\" style=\"background-color: lightgrey\" >QuestionBank  Inserted : 0 <br/>"
                    . "File are not uploaded because we found errors in the file. Error are highlighted in yellow inside the Excel Sheet. To view the errors <a href=\"" . url('/') . "/" . $questionbank_Error_name . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Download Panel Error Excel Sheet\" download class=\"btn btn-link \" style=\"color: red;\" >Click here</a></p>";
                $result_show['result'] = 0;
            }
        } else if ($check_if_error_in_document == "No") {
            Log::info('eros');

            $questionupload = false;
            $answersupload = false;
            $oquestionupload = false;
            $oanswersupload = false;
            $history = $qbhistory->updateQuestionBankHistory($input, $qbid, 4, $history_id, $filepath = $storage_dummy_folder_name, $message = "QuestionBank uploaded successfully");




      if ($input['category'] != 3 && $this->championship_data == null) {

    Log::info('QB DEBUG: BULK INSERT START');

    Log::info('QB DEBUG COUNTS', [
        'questions_count' => count($insert_data),
        'chapter_question_count' => count($insert_chapter_question),
        'topic_question_count' => count($insert_topic_question),
        'answers_count' => count($extra_option),
        'concept_count' => count($concept_insert_data),
        'solution_count' => count($solution_insert_data),
        'solution_image_count' => count($solution_option),
    ]);

    Log::info('QB SAMPLE QUESTION', $insert_data[0] ?? []);
    Log::info('QB SAMPLE ANSWER', $extra_option[0] ?? []);
    Log::info('QB SAMPLE TOPIC QUESTION', $insert_topic_question[0] ?? []);

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    Log::warning('QB DEBUG: FOREIGN KEY CHECKS DISABLED');

    DB::beginTransaction();

    try {

        $questionupload = $question->bulkQuestionUpload($insert_data);
        Log::info('QB DEBUG: questionupload', ['result' => $questionupload]);

        $chapterQuestionUpload = $chapter_question->bulkChapterQuestionUpload($insert_chapter_question);
        Log::info('QB DEBUG: chapterQuestionUpload', ['result' => $chapterQuestionUpload]);

        // $topicQuestionUpload = $topic_question->bulkTopicQuestionUpload($insert_topic_question);
        // Log::info('QB DEBUG: topicQuestionUpload', ['result' => $topicQuestionUpload]);

        $answersupload = $answer->bulkAnswersUpload($extra_option);
        Log::info('QB DEBUG: answersupload', ['result' => $answersupload]);

        $conceptUpload = $concept->bulkConceptUpload($concept_insert_data);
        Log::info('QB DEBUG: conceptUpload', ['result' => $conceptUpload]);

        $solutionsUpload = $solution->bulkSolutionsUpload($solution_insert_data);
        Log::info('QB DEBUG: solutionsUpload', ['result' => $solutionsUpload]);

        $solutionImageUpload = $solutionImage->bulkSolutionImageUpload($solution_option);
        Log::info('QB DEBUG: solutionImageUpload', ['result' => $solutionImageUpload]);

        if (
            $questionupload !== true ||
            $chapterQuestionUpload !== true ||
            $answersupload !== true
        ) {
            throw new \Exception('One or more bulk inserts failed');
        }

        DB::commit();
        Log::info('QB DEBUG: ALL BULK INSERTS COMMITTED SUCCESSFULLY');

    } catch (\Throwable $e) {
        dd($e);

        DB::rollBack();

        Log::error('QB DEBUG: BULK INSERT FAILED', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

    } finally {

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        Log::warning('QB DEBUG: FOREIGN KEY CHECKS ENABLED BACK');
    }


}





            else if ($this->championship_data != null) {  // don't store questions until championship is created

             Log::error('QB SKIPPED BULK INSERT', [
        'category' => $input['category'] ?? null,
        'championship_data' => $this->championship_data,
    ]);
            } else {

                $answers = '';


                $delete_question_ids = $question->getQuestionId($qbid);

                $delete_question_id = array_column($delete_question_ids, 'id');


                if (!empty($delete_question_id)) {

                    $delete_question_id_answers = $answer->deleteQuestionIdAnswers($delete_question_id);

                    if ($delete_question_id_answers == 1) {

                        $this->deleteimages($qbid, $delete_question_id);

                        $delete = DB::table('question')->where('question_banks_id',  $qbid)->delete();

                        if ($delete > 0) {

                            $oquestionupload = $question->bulkQuestionUpload($insert_data);
                            $ochapterQuestionUpload = $chapter_question->bulkChapterQuestionUpload($insert_chapter_question);
                            // $otopicQuestionUpload = $topic_question->bulkTopicQuestionUpload($insert_topic_question);
                            $oanswersupload = $answer->bulkAnswersUpload($extra_option);
                            $conceptUpload = $concept->bulkConceptUpload($concept_insert_data);
                            $solutionsUpload = $solution->bulkSolutionsUpload($solution_insert_data);
                            $solutionImageUpload = $solutionImage->bulkSolutionImageUpload($solution_option);
                        }
                    }
                } else {

                    $oquestionupload = $question->bulkQuestionUpload($insert_data);
                    $ochapterQuestionUpload = $chapter_question->bulkChapterQuestionUpload($insert_chapter_question);
                    // $otopicQuestionUpload = $topic_question->bulkTopicQuestionUpload($insert_topic_question);
                    $oanswersupload = $answer->bulkAnswersUpload($extra_option);
                    $conceptUpload = $concept->bulkConceptUpload($concept_insert_data);
                    $solutionsUpload = $solution->bulkSolutionsUpload($solution_insert_data);
                    $solutionImageUpload = $solutionImage->bulkSolutionImageUpload($solution_option);
                }
            }

            /*
/*
if ($this->championship_data != null)  // initiated via championship
{
    if ($this->championship_data['excel_question_select'] <= count($insert_data)) {

        $this->championship_data['total_questions'] =
            (int)($this->championship_data['excel_question_select'] ?? 0)
            + (int)($this->championship_data['qb_question_select'] ?? 0);

        try {
            DB::beginTransaction();

            $championship = Championship::createChampionship(
                $this->championship_data,
                $this->storage_dummy_folder_name ?? null,
                $input['subjective_pdf_upload'] ?? null
            );

            $data = json_decode($championship->getContent(), true);

            foreach ($insert_data as $key => $insert) {
                $insert_data[$key]['championship_id'] = $data['id'];
            }

            if ($championship->getStatusCode() == 200) {

                $questionupload = $question->bulkQuestionUpload($insert_data);
                $chapterQuestionUpload = $chapter_question->bulkChapterQuestionUpload($insert_chapter_question);
                $topicQuestionUpload = $topic_question->bulkTopicQuestionUpload($insert_topic_question);
                $answersupload = $answer->bulkAnswersUpload($extra_option);
                $conceptUpload = $concept->bulkConceptUpload($concept_insert_data);
                $solutionsUpload = $solution->bulkSolutionsUpload($solution_insert_data);
                $solutionImageUpload = $solutionImage->bulkSolutionImageUpload($solution_option);

                $response = Championship::createChampionshipQB($data['championship']);
                if ($response->getStatusCode() != 200) {
                    throw new Exception("Championship QB failed");
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
*/

            /* NORMAL QUESTION BANK FLOW */

            if ($questionupload == true && $answersupload == true && $chapterQuestionUpload == true) {

                $result_show['result'] = 1;
                $val = $counter - 2;
                File::Delete('/Documents/question_Error.xls');
                $result_show["message"] =
                    "<p align=\"left\" style=\"background-color: lightgreen\">
            QuestionBank Inserted : $val <br/>
            QuestionBank Uploaded Successfully
        </p>";
            }

            if ($oquestionupload == true && $oanswersupload == true && $ochapterQuestionUpload == true) {

                $result_show['result'] = 1;
                $val = $counter - 2;
                File::Delete('/Documents/question_Error.xls');
                $result_show["message"] =
                    "<p align=\"left\" style=\"background-color: lightgreen\">
            QuestionBank overwrite : $val <br/>
            QuestionBank Uploaded Successfully
        </p>";
            }
        }

        $mailemails = auth()->user()->email;
        if (!empty($result_show)) {  //send email

            Mail::send('emails.welcome', ["data" => $result_show['message']], function ($messages)  use ($mailemails) {
                $messages->to($mailemails);
                $messages->cc('sunil@datavoice.co.in');
                $messages->bcc('testing@datavoice.co.in');
                // $messages->bcc('yashas@datavoice.co.in');
                $messages->bcc('lakshadeep@datavoice.co.in');
                $messages->bcc('bhagwan@datavoice.co.in');

                $messages->subject('Questionbank Upload Status');
                $messages->from('noreply@datavoice.co.in');
            });
        }
                  Log::info('QB DEBUG: end');
                  DB::statement('SET FOREIGN_KEY_CHECKS=1');
Log::warning('QB DEBUG: FOREIGN KEY CHECKS ENABLED');


    }
}
