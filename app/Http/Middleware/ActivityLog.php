<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Level;
use App\Models\PackageType;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Courier;
use App\Models\Module;
use App\Models\Sms;
use App\Models\StudyMaterialV1;
use App\Models\Admin;
use App\Models\Associate;
use App\Models\User;
use App\Models\Blog;
use App\Models\SpinWheelCampaign;
use App\Models\SpinWheelSegment;
use App\Models\HighPriorityNotification;
use App\Models\HolidayOffer;
use App\Models\FreeResource;
use App\Models\Banner;
use App\Models\Section;
use App\Models\Package;
use App\Models\Professor;
use App\Models\Coupon;
use App\Models\PrivateCoupon;
use App\Models\CustomTestimonial;
use App\Models\Testimonial;
use App\Models\ThirdPartyAgent;
use App\Models\Quiz\Instruction;
use App\Models\Quiz\Paragraph;
use App\Models\Quiz\Test;
use App\Models\Quiz\Question;
use App\AdminCourse;
use App\ScormPackage;

class ActivityLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (isset($request->route()->getAction()['as'])) {
            $activity = $request->route()->getAction()['as'];
            $action = explode('.', $activity);
            if (in_array("store", $action, TRUE)) {
                $actn='Store';
            }else if(in_array("update", $action, TRUE)){
                $actn='Update';

            }else if(in_array("delete", $action, TRUE)){
                $actn='Delete';

            }else if(in_array("destroy", $action, TRUE)){
                $actn='Delete';

            } else {
                
  $actn = '';

            }

          
        } else {
            $actn = 'index';
        }

            $dataold='';
            $datanew='';
        if (($request->ajax() && $actn != 'index') || (!$request->ajax())   && $request->path() != 'home' && $request->path() != 'admin-activity') {
            if($request->input() && $actn=='Update'){
              $path=$request->path();
              $path_exp=explode("/",$path);
              $id=$path_exp[1];
              $module=$path_exp[0];
              // if($module=='courses'){
              //   $course=Course::find($id);
              //   $course1['name']=$course->name;
              //   $course1['display']=$course->display;
              //   $dataold=base64_encode(serialize($course1));
              //  // dd($request->except(['_token', '_method']));
              //   $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
                
              // }
              if($module=='courses'){
    $course = ScormPackage::find($id); 
    $course1['name'] = $course->title; 
    $course1['display'] = $course->description ?? ''; 
    $dataold = base64_encode(serialize($course1));
    $datanew = base64_encode(serialize($request->except(['_token', '_method'])));
}

              
              else if($module=='levels'){
                $level=Level::find($id);
                $level1['name']=$level->name;
                $level1['course_id']=$level->course_id;
                $level1['display']=$level->display;
                $dataold=base64_encode(serialize($level1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }
              else if($module=='type'){
                $type=PackageType::find($id);
                $type1['name']=$type->name;
                $type1['course_id']=$type->course_id;
                $type1['level_id']=$type->level_id;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              } else if($module=='subjects'){
                $type=Subject::find($id);
                $type1['subject']=$type->name;
                $type1['course_id']=$type->course_id;
                $type1['level_id']=$type->level_id;
                $type1['package_type_id']=$type->package_type_id;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='chapters'){
                $type=Chapter::find($id);
                $type1['chapter']=$type->name;
                $type1['course_id']=$type->course_id;
                $type1['level_id']=$type->level_id;
                $type1['package_type_id']=$type->package_type_id;
                $type1['subject_id']=$type->subject_id;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='couriers'){
                $type=Courier::find($id);
                $type1['name']=$type->name;
                $type1['url']=$type->url;
                $type1['status']=$type->status;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='modules'){
                $type=Module::find($id);
                $type1['name']=$type->name;
                $type1['course_id']=$type->course_id;
                $type1['level_id']=$type->level_id;
                $type1['package_type_id']=$type->package_type_id;
                $type1['subject_id']=$type->subject_id;
                $type1['chapter_id']=$type->chapter_id;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='sms'){
                $type=Sms::find($id);
                $type1['title']=$type->title;
                $type1['template_id']=$type->template_id;
                $type1['body']=$type->body;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='study-materials'){
                $type=StudyMaterialV1::find($id);
                $type1['title']=$type->title;
                $type1['course_id']=$type->course_id;
                $type1['level_id']=$type->level_id;
                $type1['package_type_id']=$type->package_type_id;
                $type1['subject_id']=$type->subject_id;
                $type1['chapter_id']=$type->chapter_id;
                $type1['language_id']=$type->language_id;
                $type1['professor_id']=$type->professor_id;
                $type1['type']=$type->type;
                $type1['file_name']=$type->file_name;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method','study_material'])));
              }else if($module=='admins'){
                $type=Admin::find($id);
                $type1['name']=$type->name;
                $type1['role']=$type->role;
                $type1['mobile']=$type->phone;
                $type1['email']=$type->email;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='agents'){
                $type=Associate::findOrFail($id);
                $user = User::findOrFail($type->user_id);
                $type1['mobile_code']=$type->country_code;
                $type1['commission']=$type->commission;
                $type1['mobile']=$user->phone;
                $type1['name']=$user->name;
                $dataold=base64_encode(serialize($type1));
                //dd($request->input());
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='blogs'){
                //dd($request->input());
                $type=Blog::findOrFail($id);
                $type1['title']=$type->title;
                $type1['slug']=$type->slug;
                $type1['author']=$type->author;
               // $type1['category_id']=$type->category_id;
                $type1['body']=$type->body;
                $type1['images']=$type->image;
                $dataold=base64_encode(serialize($type1));
                if($request->file){
                $request['images']=$request->file->getClientOriginalName();
                }
                $datanew=base64_encode(serialize($request->except(['_token', '_method','file'])));
              }
              else if($module=='spin-wheel-campaigns'){
                $type=SpinWheelCampaign::findOrFail($id);
                $segment=SpinWheelSegment::findOrFail($id);
                $type1['title']=$type->title;
                $type1['start_date']=$type->start_date;
                $type1['end_date']=$type->end_date;
                $type1['max_budget']=$type->max_budget;
                $type1['point_validity']=$type->point_validity;
                $type1['is_published']=$type->is_published;
                $type1['segmanet_title']=$segment->title;
                $type1['success_percentage']=$segment->success_percentage;
                $type1['hits_in_hundred']=$segment->hits_in_hundred;
                $type1['color_code']=$segment->color_code;
                $type1['spin_wheel_campaign_id']=$segment->spin_wheel_campaign_id;
                $type1['value_type']=$segment->value_type;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }
              else if($module=='high-priority-notifications'){
                $type=HighPriorityNotification::findOrFail($id);
                $type1['start_at']=$type->start_at;
                $type1['end_at']=$type->end_at;
                $type1['content']=$type->content;
                $type1['status']=$type->status;
               $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }  else if($module=='holiday-scheme'){
                $type=HolidayOffer::findOrFail($id);
                $type1['name']=$type->name;
                $type1['discount_type']=$type->discount_type;
                $type1['discount_amount']=$type->discount_amount;
                $type1['cashback_amount']=$type->cashback_amount;
                $type1['cashback_type']=$type->cashback_type;
                $type1['max_cashback']=$type->max_cashback;
                $type1['min_cart_amount']=$type->min_cart_amount;
                $type1['courses']=$type->courses;
                $type1['level_id']=$type->level_id;
                $type1['package_type']=$type->package_type;
                $type1['from_date']=$type->from_date;
                $type1['to_date']=$type->to_date;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='free-resource'){
                $type=FreeResource::findOrFail($id);
                $type1['title']=$type->title;
                $type1['course_id']=$type->course_id;
                $type1['level_id']=$type->level_id;
                $type1['package_type_id']=$type->package_type_id;
                $type1['professor_id']=$type->professor_id;
                $type1['description']=$type->description;
                $type1['type']=$type->type;
                $type1['youtube_id']=$type->youtube_id;
                $type1['thumbnail_file']=$type->thumbnail_file;
                $type1['filename']=$type->file;
                $dataold=base64_encode(serialize($type1));
                if($request->document){
                $request['filename']=$request->document->getClientOriginalName();
                }
                $datanew=base64_encode(serialize($request->except(['_token', '_method','document'])));
              }else if($module=='banners'){
                $type=Banner::findOrFail($id);
                $type1['title']=$type->title;
                $type1['user_id']=$type->user_id;
                $type1['alt']=$type->alt;
                $type1['title_url']=$type->title_url;
                $type1['youtube_id']=$type->youtube_id;
                $type1['image']=$type->image;
                $dataold=base64_encode(serialize($type1));
                if($request->file){
                 $request['image']=$request->file->getClientOriginalName();
                }
                //dd($request->file->getClientOriginalName());
                $datanew=base64_encode(serialize($request->except(['_token', '_method','file'])));
              }else if($module=='sections'){
                $type=Section::findOrFail($id);
                $type1['name']=$type->name;
                $type1['is_enabled']=$type->is_enabled;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }else if($module=='packages'){
               
                $type=Package::findOrFail($path_exp[2]);
                
                $type1['type']=$type->type;
                $type1['category']=$type->category;
                $type1['name']=$type->name;
                $type1['course_id']=$type->course_id;
                $type1['level_id']=$type->level_id;
                $type1['package_type']=$type->package_type;
                $type1['subject_id']=$type->subject_id;
                $type1['chapter_id']=$type->chapter_id;
                $type1['language_id']=$type->language_id;
                $type1['price']=$type->price;
                $type1['discounted_price']=$type->discounted_price;
               // $type1['discounted_price_expire_at']=$type->discounted_price_expire_at;
                $type1['professor_revenue']=$type->professor_revenue;
                $type1['special_price']=$type->special_price;
              //  $type1['special_price_expire_at']=$type->special_price_expire_at;
                $type1['special_price_active_from']=date("Y-m-d",strtotime($type->special_price_active_from));
              
                $type1['attempt']=date("m-Y",strtotime($type->attempt));
                $type1['duration']=$type->duration;
                $type1['expiry_name']=$type->expiry_type;
                //$type1['is_cseet']=$type->is_cseet;
                $type1['video_id']=$type->video_id;
                $type1['expiry_month']=$type->expiry_month;
              //  $type1['expire_at']=$type->expire_at;
                $type1['alt']=$type->alt;
                $type1['study_material_price']=$type->study_material_price;
                $type1['description']=$type->description;
                $type1['is_mini']=$type->is_mini;
                $type1['type']=$type->is_crash_course;
                $type1['pendrive']=$type->pendrive;
                $type1['g-drive']=$type->g_drive;
                $type1['image']=$type->image;
               // $type1['selling_amount']=$type->selling_amount;
                $dataold=base64_encode(serialize($type1));
                if($request->image){
                $request['image']=$request->image->getClientOriginalName();
                }
                $datanew=base64_encode(serialize($request->except(['_token', '_method','image'])));
              }else if($module=='professors'){
                $type=Professor::findOrFail($id);
                $type1['name']=$type->name;
                $type1['title']=$type->title;
                $type1['email']=$type->email;
                $type1['country_code']=$type->country_code;
                $type1['mobile']=$type->mobile;
                $type1['description']=$type->description;
                  $type1['password_option']=$type->password_option;
                $type1['video_type']=$type->video_type;
                $type1['media_id']=$type->media_id;
                $type1['video']=$type->video;
                $type1['career_start_at']=$type->career_start_at;
                $type1['professor_revenue']=$type->professor_revenue;
                $type1['introduction']=$type->introduction;
                $type1['is_published']=$type->is_published;
                $type1['alt']=$type->alt;
                $type1['profilepic']=$type->image;
                $dataold=base64_encode(serialize($type1));
                if($request->file){
                $request['profilepic']=$request->file->getClientOriginalName();
                }
                $datanew=base64_encode(serialize($request->except(['_token', '_method','file'])));
              }else if($module=='coupons'){
                $type=Coupon::findOrFail($id);
                $type1['name']=$type->name;
                $type1['amount']=$type->amount;
                $type1['amount_type']=$type->amount_type;
                $type1['coupon_type']=$type->coupon_type;
                $type1['coupon_per_user']=$type->coupon_per_user;
                $type1['total_coupon_limit']=$type->total_coupon_limit;
                $type1['valid_from']=$type->valid_from;
                $type1['valid_to']=$type->valid_to;
                $type1['max_discount_amount']=$type->max_discount_amount;
                $type1['min_purchase_amount']=$type->min_purchase_amount;

                $private_coupon=PrivateCoupon::findOrFail($type->id);
                
                $type1['course_id']=$private_coupon->course_id;
                $type1['coupon_id']=$private_coupon->coupon_id;
                $type1['level_id']=$private_coupon->level_id;
                $type1['subject_id']=$private_coupon->subject_id;
                $type1['package_type_id']=$private_coupon->package_type_id;
                $type1['student_id']=$private_coupon->student_id;
                $type1['professor_id']=$private_coupon->professor_id;
                $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              } else if($module=='custom-testimonials'){
                $type=CustomTestimonial::findOrFail($id);
                $type1['name']=$type->name;
                $type1['testimonial']=$type->testimonial;
                $type1['publish']=$type->publish;
                $type1['img']=$type->image;
                $dataold=base64_encode(serialize($type1));
                if($request->image_file){
                $request['img']=$request->image_file->getClientOriginalName();
                }
                $datanew=base64_encode(serialize($request->except(['_token', '_method','image_file'])));
              }  else if($module=='student-testimonials'){
                $type=Testimonial::findOrFail($id);
                $type1['publish']=$type->publish;
                $type1['testimonial']=$type->testimonial;
               
               
               $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }  else if($module=='third-party-agents'){
                $type=ThirdPartyAgent::findOrFail($id);
                $type1['phone']=$type->phone;
                $type1['country_code']=$type->country_code;
              
                $user=User::findOrFail($type->user_id);
                $type1['name']=$user->name;
               
               
               $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }    else if($module=='quiz' && $path_exp[1]=='instruction' ){
             
                $type=Instruction::findOrFail($path_exp[2]);
              
                $type1['name']=$type->name;
                $type1['description']=$type->description; 
                $type1['assigned_to']=$type->assigned_to;
                $type1['status']=$type->status;
                $type1['added_by']=$type->added_by;
                $type1['updated_by']=$type->updated_by;
               
               
               $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              } 
              else if($module=='quiz' && $path_exp[1]=='paragraph' ){
              
                $type=Paragraph::findOrFail($path_exp[2]);
              
                $type1['name']=$type->name;
                $type1['description']=$type->description; 
                $type1['assigned_to']=$type->assigned_to;
                $type1['status']=$type->status;
                $type1['added_by']=$type->added_by;
                $type1['updated_by']=$type->updated_by;
               
               
               $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              } 
              else if($module=='quiz' && $path_exp[1]=='test' ){
              
                $type=Test::findOrFail($path_exp[2]);
              
                $type1['name']=$type->name;
                $type1['instruction_id']=$type->instruction_id; 
                $type1['test_type']=$type->test_type;
                $type1['sections']=$type->sections;
                $type1['is_difficulty']=$type->is_difficulty;
                $type1['show_camera']=$type->show_camera;
                $type1['attempt']=$type->attempt;
                $type1['negative']=$type->negative; 
                $type1['is_feedback']=$type->is_feedback;
                $type1['feedback_type']=$type->feedback_type;
                $type1['ques_ordered']=$type->ques_ordered;
                $type1['ans_ordered']=$type->ans_ordered;
                $type1['status']=$type->status;
                $type1['created_by']=$type->created_by;
                $type1['updated_by']=$type->updated_by;
               
               
               $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              }  else if($module=='quiz' && $path_exp[1]=='question' ){
              
                $type=Question::findOrFail($path_exp[2]);
              
                $type1['question']=$type->question;
                $type1['question_desc']=$type->question_desc; 
                $type1['difficulty']=$type->difficulty;
                $type1['instruction_id']=$type->instruction_id;
                $type1['score']=$type->score;
                $type1['status']=$type->status;
                $type1['time']=$type->time;
                $type1['correct_feedback']=$type->correct_feedback; 
                $type1['incorrect_feedback']=$type->incorrect_feedback;
                $type1['partially_feedback']=$type->partially_feedback;
                $type1['paragraph_id']=$type->paragraph_id;
                $type1['ans_ordered']=$type->ans_ordered;
                $type1['status']=$type->status;
                $type1['created_by']=$type->created_by;
                $type1['updated_by']=$type->updated_by;
               
               
               $dataold=base64_encode(serialize($type1));
                $datanew=base64_encode(serialize($request->except(['_token', '_method'])));
              } 
              
            
            }
            $admin_log = new AdminLog();
            $admin_log->user_id = Auth::user()->id;
            $admin_log->role_id = Auth::user()->role;
            $admin_log->ip_address = request()->ip();
            $admin_log->activity = $request->path();
            $admin_log->session_token = session()->get('access_token');
            $admin_log->server_var = base64_encode(serialize($_SERVER));;
            $admin_log->original_value = $dataold;
            $admin_log->modified_value = $datanew;
            $admin_log->action = $actn;

            $admin_log->save();
        }
        return $next($request);
    }
}
