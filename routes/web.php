<?php

use App\Http\Controllers\AdminCourseController;
use App\Http\Middleware\ReportAdminMiddleware;
use App\Http\Middleware\ReportingMiddleware;
use App\Http\Middleware\ThirdPartyAgentAdminMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ContentManagerAdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\AssistantMiddleware;
use App\Http\Middleware\BackOfficeManagerMiddleware;
use App\Http\Middleware\FinanceManagerMiddleware;
use App\Http\Middleware\ActivityLog;
use App\Http\Middleware\JuniorAdminMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/** @noinspection PhpParamsInspection */

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth::routes([
//     'register' => false
// ]);
Auth ::routes();


Route::middleware(['auth', ReportAdminMiddleware::class, ContentManagerAdminMiddleware::class, ThirdPartyAgentAdminMiddleware::class, SuperAdminMiddleware::class, AssistantMiddleware::class,ReportingMiddleware::class,BackOfficeManagerMiddleware::class,ActivityLog::class,JuniorAdminMiddleware::class,FinanceManagerMiddleware::class])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('courses', 'CourseController');
    Route::post('courses/change-order', 'CourseController@changeOrder')->name('courses.change-order');

    Route::resource('third-party-agents', 'ThirdPartyAgentController');

    Route::resource('third-party-orders', 'ThirdPartyOrderController');
    Route::post('third-party-orders/{id}/cancel','ThirdPartyOrderController@cancel')->name('third-party-orders.cancel');

    Route::get('/get-student', 'ThirdPartyOrderController@getStudent');

    Route::resource('levels', 'LevelController');
    Route::post('levels/change-order', 'LevelController@changeOrder')->name('levels.change-order');

    Route::resource('subjects', 'SubjectController');

    Route::resource('chapters', 'ChapterController');

    Route::resource('study-materials', 'StudyMaterialsController');

    Route::resource('refunds', 'RefundController');

    Route::post('update-study-material', 'ChapterController@updateStudyMaterial');

    Route::post('delete-study_materials', 'ChapterController@deleteStudyMaterials');

    Route::resource('modules', 'ModuleController');

    Route::resource('professors', 'ProfessorController');
    Route::get('update-professor-career-start-at', 'ProfessorController@updateCareer');

    Route::resource('high-priority-notifications', 'HighPriorityNotificationController');

    Route::post('publish-professor-video', 'ProfessorController@publishProfessorVideo');

    Route::post('validate-phone', 'AdminController@validatePhone');

    Route::resource('banners', 'BannerController');

    Route::post('videos/change', 'VideoController@change')->name('videos.change');
    Route::post('videos/{id}/archive/add', 'VideoController@addToArchive')->name('videos.archive.add');
    Route::post('videos/{id}/archive/remove', 'VideoController@removeFromArchive')->name('videos.archive.remove');
    Route::resource('videos', 'VideoController');
    Route::resource('s3-videos', 'VideoControllerS3');
    Route::post('s3-videos/media_id_check', 'VideoControllerS3@checkMediaId')->name('video.mediaId.check');
    //Rulebook Routes
    Route::resource('rule-book', 'RuleBookController');
    Route::get('/search-packages-rule-book', 'RuleBookController@searchPackages')->name('search.rulebook.packages');
    Route::get('/check-package-rulebook-exists', 'RuleBookController@checkPackageRulebookExists')->name('check.package.rulebook.exists');
    Route::post('s3rulebook/uploadS3', 'RuleBookController@uploadS3')->name('s3rulebook.uploadS3.index');
    
    Route::post('videos-add-to-archeive', 'VideoController@archeieveSelectedVideos');

    Route::get('fetch-published-videos', 'VideoController@fetchPublishedVideos');

    Route::get('fetch-unpublished-videos', 'VideoController@fetchUnPublishedVideos');

    // ----------------------------- Added By TE ----------------------------------------//
    Route::get('packages/subject/fetch-published-videos-for-package', 'SubjectController@fetchPublishedVideos');

    Route::get('fetch-answered-questions' , 'QuestionController@fetchAnsweredQuestions');

    Route::get('fetch-pending-questions' , 'QuestionController@fetchPendingQuestions');

    Route::resource('sms', 'SmsController');

    Route::get('custom-notifications/templatebody/{id}','CustomNotificationController@templatebody');

    Route::get('/getlevels/ajax/{id}','SectionController@getlevels')->name('courses.levels');
    Route::get('/getsubjects/ajax/{id}','SectionController@getSubjects')->name('levels.subjects');
    Route::get('/getprofessors/ajax/{id}','SectionController@getprofessors')->name('subjects.professors');
    Route::post('package-add-to-archeive', 'PackageController@archeieveSelectedpackage');
    Route::post('unlink_demo_package', 'FreeResourceController@UnlinkPackage');
    Route::get('/packages/getvideodetails/{id}','PackageController@getvideodetails');
    Route::post('unlink_demo_video', 'PackageController@UnlinkVideo');
    Route::get('/chapter-module/ajax/{id}','ModuleController@ChapterModule')->name('chapter.module');
    Route::resource('type', 'PackageTypeController');
    Route::get('updateDuration', 'PackageDuration@update');

    Route::get('student-analytics' ,'Reports\StudentAnalyticsController@index');

    Route::get('invoiceRegenerate','InvoiceRegenerate@search');
    

    Route::post('invoice/update','InvoiceRegenerate@update');
    Route::get('invoice/search','InvoiceRegenerate@search')->name('invoice.search');
    Route::resource('count-setting','HomePageCountController');


    Route::get('admin-activity' ,'Reports\AdminActivity@index');
    Route::get('admin-activity-action' ,'Reports\AdminActivity@indexaction');
    Route::get('get-server_var', 'Reports\AdminActivity@getresponse');
    Route::get('get-edit-log', 'Reports\AdminActivity@getEditLog');

    Route::resource('holiday-scheme','HolidaySchemeController');

    

    Route::resource('user-list','MobileUserController');
    Route::resource('holiday-scheme','HolidaySchemeController');
    Route::get('holiday-scheme-usage','HolidaySchemeController@usage_report');
    Route::post('publishOffer','HolidaySchemeController@publishOffer')->name('holiday-scheme.publishOffer');
    Route::get('deal_of_day','DealOfDayController@index');
    Route::post('deal_ofday/update','DealOfDayController@update')->name('deal_of_day.update');
    Route::get('/getPackageData/{id}','DealOfDayController@getPackagedata');
    Route::get('get-levels-by-course','PackageTypeController@getLevelsByCourse');
    
    Route::get('/gettypes/ajax/{id}','PackageTypeController@getTypes')->name('levels.types');
    Route::get('/get-subjects-by-level', 'PackageTypeController@getSubjectsByLevels');
    Route::get('/getprofessorPackages/ajax/{id}','QuestionController@getprofessorPackages');
    Route::get('order-revenue','OrderRevenueController@index');
    Route::post('order-revenue/export', 'OrderRevenueController@export');
    Route::resource('techsupport','TechSupportController');
    Route::post('update-remark','TechSupportController@update_remark')->name('techsupport.update-remark');
    
    //----------------------Email Support --------------------------------------//

    Route::resource('email-support','EmailSupportController');

    Route::get('fetch-pending' , 'EmailSupportController@InProgress');

    Route::get('fetch-completed' , 'EmailSupportController@completed');

    Route::post('update-status','EmailSupportController@update_status')->name('email.update-status');

    Route::get('/getStudentData/{id}','EmailSupportController@getdata');

    Route::post('update_extension','EmailSupportController@updateExtension')->name('email.update_extension');

    Route::post('/getPackvalidity','EmailSupportController@getPackvalidity')->name('email.getPackvalidity');

    //---------------------------Email support Ends --------------------------------//

    /********************Email Log***********/
    Route::resource('email-log', 'EmailLogController');
    
    //-----------------------------------------------------------------------------------//

    Route::get('studio-upload-videos', 'VideoController@studioVideos');

    Route::get('sync-videos', 'VideoController@syncVideos');

    Route::get('merge-videos/{id}', 'VideoController@mergeVideos');

    Route::post('merge-edugulp-videos', 'VideoController@mergeEdugulpVideos');

    Route::get('packages/videos', 'PackageController@videos')->name('packages.videos');

    Route::resource('packages', 'PackageController');
    Route::post('packages/archive/add/{id}', 'PackageController@addToArchive')->name('packages.archive.add');
    Route::post('packages/archive/remove/{id}', 'PackageController@removeFromArchive')->name('packages.archive.remove');


    Route::resource('package-study-materials', 'PackageStudyMaterialController');

    Route::resource('packages.package-study-materials', 'PackageStudyMaterialController')->shallow()->only('index');

    Route::resource('packages.subjects', 'Package\SubjectController')->shallow()->only('index');

    Route::resource('packages.customizes', 'Package\CustomizeController')->shallow()->only('index');

    Route::get('fetch-professors-from-videos', 'PackageStudyMaterialController@fetchProfessorsFromVideos');

    Route::get('fetch-professors-from-packages', 'PackageStudyMaterialController@fetchProfessorsFromPackages');
    Route::get('data-for-study-materials', 'PackageStudyMaterialController@getDataForStudyMaterials');

    Route::resource('package-reports', 'PackageReportsController');

    Route::resource('admins', 'AdminController');

    Route::resource('agents', 'AgentController');
    Route::resource('sections', 'SectionController');
    Route::get('/course-levels/ajax/{id}','SectionController@Courselevels')->name('course.level');
    Route::get('/level-subjects/ajax/{id}','SubjectController@LevelSubjects')->name('level.subject');
    Route::get('/subject-chapters/ajax/{id}','ChapterController@SubjectChapters')->name('subject.chapter');
    Route::get('/subject-professor/ajax/{id}','SubjectController@SubjectProfessors')->name('subject.professor');

    Route::post('sections/change-order', 'SectionController@changeOrder')->name('sections.change-order');
    Route::get('sections/{id}/section-packages', 'SectionController@createSectionPackages');
    Route::post('sections/{id}/section-packages', 'SectionController@storeSectionPackages');
    Route::get('sections/{id}/section-packages/order', 'SectionController@changePackageOrder');
    Route::post('sections/{id}/section-packages/order', 'SectionController@savePackageOrder');
    Route::post('sections/{id}/destroy-selected-packages', 'SectionController@destroySelectedPackages');

    Route::resource('section-packages', 'SectionPackageController');
    Route::resource('packages.section-packages', 'SectionPackageController')->shallow();

    Route::resource('settings', 'SettingController');

    Route::resource('custom-notifications', 'CustomNotificationController')->only('index', 'create', 'store');
//    Route::get('custom-notifications/create', 'CustomNotificationController@create');

    Route::post('export-call-requests', 'CallRequestController@export');

    Route::resource('call-requests', 'CallRequestController');

    Route::resource('professor-revenues', 'ProfessorRevenueController');

    Route::resource('free-resource', 'FreeResourceController');

    Route::resource('coupons', 'CouponController');

    Route::resource('package-materials', 'PackageStudyMaterialController');

    Route::post('update-coupon-status/{id}', 'CouponController@updateStatus');

    Route::resource('student-testimonials', 'StudentTestimonialController');

    Route::resource('custom-testimonials', 'CustomTestimonialController');

    Route::resource('j-money-settings', 'JMoneySettingsController');

    Route::resource('j-money', 'JMoneyController');

    Route::get('students/import', 'StudentController@getImport');
    Route::post('students/import', 'StudentController@postImport')->name('students.import.store');
    Route::resource('students', 'StudentController');

    Route::resource('orders', 'OrderController',['names'=>'orders']);
    Route::get('invoice_generate/{id}','OrderController@invoiceGen');
    Route::get('list-coupons', 'CouponController@listCoupons');

    Route::post('packages/publish/{id}', 'PackageController@publish')->name('packages.publish');
    Route::get('packages/edit/{id}', 'PackageController@edit')->name('packages.edit');
    Route::post('packages/un-publish/{id}', 'PackageController@unPublish')->name('packages.un-publish');
    Route::post('videos/publish', 'VideoController@multiplePublish')->name('videos.publish.multiple');
    Route::post('videos/publish/{id}', 'VideoController@publish')->name('videos.publish');
    Route::post('videos/un-publish', 'VideoController@multipleUnPublish')->name('videos.un-publish.multiple');
    Route::post('videos/un-publish/{id}', 'VideoController@unPublish')->name('videos.un-publish');

    Route::get('subjects/level_from_course/{course_id}', 'SubjectController@level_from_course');

//courses new route
    // Route::resource('courses', AdminCourseController::class);    
   Route::resource('courses', 'AdminCourseController');


    Route::group(['prefix' => 'packages', 'as' => 'packages.'], function () {
        Route::resource('chapter', 'Package\ChapterController')->except('index');
        Route::resource('subject', 'Package\SubjectController');
        Route::resource('customize', 'Package\CustomizeController');
        Route::resource('settings', 'Package\SettingController');
        Route::resource('prebook-package', 'PrebookController');
        Route::get('professor/revenues', 'PackageController@professorRevenue');
        Route::post('professor/revenues/export', 'PackageController@export');
        Route::get('professor/revenues/update/{id}', 'PackageController@professorRevenueUpdate');
        Route::get('/{id}/videos', 'PackageController@createVideosPackages');
        Route::post('/{id}/videos', 'Package\ChapterController@addPackageVideos');
        Route::get('/{id}/videos/order', 'Package\ChapterController@changeOrder');
        Route::post('/{id}/videos/order', 'Package\ChapterController@saveOrder');
        Route::get('/{id}/chapter-packages', 'PackageController@createChapterPackages');
        Route::post('/{id}/chapter-packages', 'Package\SubjectController@addChapterPackages');
        Route::get('/{id}/chapter-packages/order', 'Package\SubjectController@changeOrder');
        Route::post('/{id}/chapter-packages/order', 'Package\SubjectController@saveOrder');
        Route::get('/{id}/all-packages', 'PackageController@createAllPackages');
        Route::post('/{id}/all-packages', 'Package\CustomizeController@addAllPackages');
        Route::get('/{id}/all-packages/order', 'Package\CustomizeController@changeOrder');
        Route::post('/{id}/all-packages/order', 'Package\CustomizeController@saveOrder');
        Route::get('/{id}/study-materials', 'PackageController@addOrEditStudyMaterial');
        Route::post('/{id}/study-materials', 'PackageController@addStudyMaterials');
        Route::post('/{id}/publish', 'PackageController@markAsPublished');
        Route::post('/{id}/toggle-prebook', 'PackageController@togglePrebook');
        Route::post('/professor-revenue/store', 'PackageController@import');
    });

    Route::get('package-extensions', 'PackageExtensionController@index')->name('package-extensions.index');
    Route::get('package-extensions/{id}/add-extension', 'PackageExtensionController@addExtension')->name('package-extensions.add-extension');
    Route::post('package-extensions', 'PackageExtensionController@store')->name('package-extensions.store');
    Route::delete('package-extensions/{id}', 'PackageExtensionController@destroy')->name('package-extensions.destroy');

    Route::resource('subject', 'Package\SubjectController');

    Route::get('all-packages', 'PackageController@allPackages');
    Route::get('drafted-packages', 'PackageController@draftedPackages');
    Route::get('published-packages', 'PackageController@publishedPackages');
    Route::get('archived-packages', 'PackageController@archivedPackages');



    Route::group(['prefix' => 'third-party', 'as' => 'third-party.'], function () {

        Route::resource('students', 'ThirdParty\StudentController')->only('store');
    });

    Route::resource('agent-orders','ThirdParty\OrderController');

    Route::get('get-order-response', 'OrderController@getOrderResponse');
    Route::get('get-payment-response', 'OrderController@getPaymentResponse');
    Route::get('fetch-order-items', 'OrderController@fetchOrderItems');
    Route::post('assign-packages', 'OrderController@assignPackages');
    Route::post('update-order', 'OrderController@updateOrder');
    Route::resource('salesrevenue', 'SalesRevenueController');
    Route::post('export-salesrevenue-report', 'SalesRevenueController@export');

    //----------------------- Added By TE ----------------------------//

    Route::get('fetch-order-details', 'OrderController@fetchOrderDetails');

    //------------------------- TE Ends ------------------------------//

    Route::resource('prepaid-packages', 'PrepaidPackageController')->only('index', 'show', 'store','edit','update');
    Route::resource('sales', 'SaleController');
    Route::post('export-sales-report', 'SaleController@export');
    Route::post('create-student', 'PrepaidPackageController@createStudent');
    Route::post('check-if-package-assigned', 'PrepaidPackageController@checkPackageAssigned');

    /********************Added by TE ************************************/

    Route::get('fetch-sales-details', 'SaleController@fetchSaleDetails');
    Route::resource('cseet-students','CseetController');
    Route::get('cseet/status-rejected/{id}', [ \App\Http\Controllers\CseetController::class, 'StatusRejected' ])->name('cseet.status_rejected');
    Route::get('cseet/status-accepted/{id}', [ \App\Http\Controllers\CseetController::class, 'StatusAccepted' ])->name('cseet.status_accepted');


    /*********************TE Ends ***********************************/
    Route::resource('can-not-find-enquire','CanNotFindEnquireController');
    Route::get('change-banners-order', 'BannerController@changeOrder');
    Route::get('change-resources-order', 'FreeResourceController@changeOrder');

    Route::resource('purchases', 'PurchaseController')->only('index', 'update','test');

    Route::resource('couriers', 'CourierController');
    Route::get('courier','CourierController@getCouriers');

   // Route::get('test', 'PurchaseController@test');
    Route::resource('spin-wheel-campaigns', 'SpinWheelCampaignController');

    Route::resource('campaign-registrations', 'CampaignRegistrationController');

    Route::resource('blogs', 'BlogController')->except('show');
    Route::post('blogs/images', 'BlogController@uploadImage')->name('blogs.images.store');
    Route::post('blogs/{id}/publish', 'BlogController@publish')->name('blogs.publish');
    Route::post('blogs/change-order', 'BlogController@changeOrder')->name('blogs.change-order');
    Route::get('blogs/{id}/preview', 'BlogController@preview')->name('blogs.preview');

    // Quiz start
    Route::namespace('Quiz')->prefix('quiz')->name('quiz.')->group(function () {
        // Route::resource('admin', 'AdminController');
        // Route::get('getPassword', 'AdminController@getPassword')->name('getPassword');
        Route::get('dashboard', 'DashboardController@index')->name('dashboard');
        Route::get('user', 'UserController@index')->name('user');
        Route::get('user/edit/{id}', 'UserController@edit')->name('user.edit');
        Route::resource('board', 'BoardController');
        Route::get('board/destroy/{ID}','BoardController@destroy');
        Route::resource('grade', 'GradeController');
        Route::post('getGrade', 'GradeController@getGrade')->name('getGrade');
        Route::get('grade/destroy/{id}', 'GradeController@destroy');
        Route::resource('subject', 'SubjectController');
        Route::post('getSubject', 'SubjectController@getSubject')->name('getSubject');
        Route::get('subject/destroy/{id}', 'SubjectController@destroy');
        Route::resource('power', 'PowerController');

        Route::resource('chapter', 'ChapterController');
        Route::post('getChapter', 'ChapterController@getChapter')->name('getChapter');
        Route::get('chapter/destroy/{id}', 'ChapterController@destroy');
        Route::resource('concept', 'ConceptController');
        Route::post('getConcept', 'ConceptController@getConcept')->name('getConcept');
        Route::get('concept/destroy/{id}', 'ConceptController@destroy');
        Route::resource('instruction', 'InstructionController');
        Route::get('instruction/view/{ID}', 'InstructionController@view')->name('instruction.view');
        Route::get('instruction/destroy/{ID}', 'InstructionController@destroy');
        Route::resource('paragraph', 'ParagraphController');
        Route::get('paragraph/view/{ID}', 'ParagraphController@view')->name('paragraph.view');
        Route::get('paragraph/destroy/{ID}', 'ParagraphController@destroy');
        Route::resource('taxonomy', 'TaxonomyController');
        Route::get('taxonomy/destroy/{ID}', 'TaxonomyController@destroy');
        Route::resource('learning_stage', 'LearningStageController');
        Route::get('learning_stage/destroy/{ID}', 'LearningStageController@destroy');
        Route::resource('module', 'ModuleController');
        Route::post('getConcept', 'ConceptController@getConcept')->name('getConcept');
        Route::post('getModules', 'ModuleController@getModules')->name('getModules');
        Route::get('module/delete/{ID}','ModuleController@destroy');
        Route::post('getQuestions', 'ModuleController@getQuestions')->name('getQuestions');
        Route::resource('test', 'TestController');
        Route::resource('modules', 'TestModulesController');
        Route::get('modules/destroy/{ID}','TestModulesController@destroy');
        Route::get('test/getStep2/{ID}', 'TestController@getStep2')->name('test.getStep2');
        Route::post('test/step2-submit/{QID}', 'TestController@Step2Submit')->name('test.step2-submit');
        Route::post('test/step2-submit-auto/{QID}', 'TestController@Step2SubmitAuto')->name('test.step2-submit-auto');
        Route::get('test/getStep3/{ID}', 'TestController@getStep3')->name('test.getStep3');
        Route::post('test/step3-submit/{QID}', 'TestController@Step3Submit')->name('test.step3-submit');
        Route::post('test/addModule', 'TestController@addModule')->name('test.addModule');
        Route::post('test/editModule', 'TestController@editModule')->name('test.editModule');
        Route::post('test/updateModule', 'TestController@updateModule')->name('test.updateModule');
        Route::get('test/delete/{id}', 'TestController@destroy');

        Route::resource('question', 'QuestionController');
        Route::get('question/destroy/{id}','QuestionController@destroy');
        Route::get('uploadExcelView', 'QuestionController@uploadExcelView')->name('uploadExcelView');
        Route::post('uploadQuestionExcel', 'QuestionController@uploadQuestionExcel')->name('uploadQuestionExcel');
        Route::resource('event', 'EventController');
        Route::get('event/destroy/{ID}','EventController@destroy');
        Route::resource('content_library', 'ContentLibraryController');
        Route::get('content_library/destroy/{id}','ContentLibraryController@destroy');
        Route::post('event/addRound', 'EventController@addRound')->name('event.addRound');
        Route::post('event/editRound', 'EventController@editRound')->name('event.editRound');
        Route::post('event/updateRound', 'EventController@updateRound')->name('event.updateRound');
        Route::get('event/deleteRound/{event_id}/{round}', 'EventController@deleteRound')->name('event.deleteRound');
        Route::post('updateImage', 'InstructionController@updateImage')->name('updateImage');
        Route::post('question/step-submit/{QID}', 'QuestionController@StepSubmit')->name('question.step-submit');
        Route::get('question/step-2/{QID}', 'QuestionController@Step2')->name('question.step-2');
        Route::post('question/step2-submit/{QID}', 'QuestionController@Step2Submit')->name('question.step2-submit');
        Route::get('question/index-para', 'QuestionController@indexPara')->name('question.index-para');
        Route::get('question/view/{ID}', 'QuestionController@view')->name('question.view');
        Route::get('test/refresh-table/{TESTID}/{SUBID?}/{CHAPID?}/{CONID?}', 'TestController@refreshTable')->name('test.refresh-table');
        Route::get('modules/refresh-table/{TESTID}/{MODID}/{SUBID?}/{CHAPID?}/{CONID?}', 'TestModulesController@refreshTable')->name('modules.refresh-table');
        Route::post('image-upload', 'DashboardController@imageUpload')->name('image-upload');
        Route::post('getSelectedChapter', 'TestController@getSelectedChapter')->name('getSelectedChapter');
        Route::post('getSelectedConcept', 'TestController@getSelectedConcept')->name('getSelectedConcept');
        Route::post('module/auto-update/{ID}', 'TestModulesController@autoUpdate')->name('module.auto-update');
        Route::get('module/getStep2/{ID}', 'TestModulesController@getStep2')->name('module.getStep2');
    });
    // Quiz end

    Route::get('users/usage', 'UserController@usage')->name('users.usage');

    Route::get('questions', 'QuestionController@index')->name('questions.index');
    Route::get('questions/professors', 'QuestionController@professors')->name('questions.professors.index');
    Route::get('questions/{id}', 'QuestionController@show')->name('questions.show');
    Route::resource('notifications', 'NotificationController')->only('store');
    Route::resource('feedback', 'FeedbackController')->only('index', 'show');
    Route::resource('feedback-list', 'FeedbackListController');
    Route::post('feedback-list/status-rejected/{id}', [ \App\Http\Controllers\FeedbackListController::class, 'StatusRejected' ])->name('feedback-list.status_rejected');
    Route::post('feedback-list/status-accepted/{id}', [ \App\Http\Controllers\FeedbackListController::class, 'StatusAccepted' ])->name('feedback-list.status_accepted');
});

Route::middleware('auth')->prefix('tables')->name('tables.')->group(function() {
    Route::get('orders', 'StudentController@getTableOrders')->name('orders');
    Route::get('cart', 'StudentController@getTableCart')->name('cart');
    Route::get('coupons', 'StudentController@getTableCoupons')->name('coupons');
    Route::get('packages', 'PrepaidPackageController@getTablePackages')->name('packages');
    Route::get('student-order-items/{id}', 'PrepaidPackageController@tableStudentOrderItems')->name('student-order-items');
    Route::get('student-transactions/{id}', 'PrepaidPackageController@tableStudentTransactions')->name('student-transactions');
    Route::get('study-materials', 'StudyMaterialsController@getTableStudyMaterials')->name('study-materials');
    Route::get('payments/{id}', 'OrderController@getPaymentDetails')->name('payments');
    Route::get('videos', 'VideoController@tableVideos')->name('videos');
});

Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
    Route::resource('agents', 'Reports\AgentController');
    Route::get('table-agent-students', 'Reports\AgentController@getTableStudents')->name('table-agent-students');
    Route::get('table-agent-orders', 'Reports\AgentController@getTableOrders')->name('table-agent-orders');

    Route::get('students-bar-data-by-year', 'StudentController@getBarDataByYear');
    Route::get('students-bar-data-by-month', 'StudentController@getBarDataByMonth');

    Route::get('orders-bar-data-by-year', 'OrderController@getBarDataByYear');
    Route::get('orders-bar-data-by-month', 'OrderController@getBarDataByMonth');

    Route::resource('professor-payouts', 'Reports\ProfessorPayoutController');
    Route::get('videos', 'Reports\VideoController@index');
    Route::post('videos/export', 'Reports\VideoController@export');
    Route::get('payments', 'Reports\PaymentController@index');
    Route::post('payments/export', 'Reports\PaymentController@export');
    Route::post('students/export', 'StudentController@export');
    Route::post('imported-students/export', 'Reports\ImportedStudentController@export');
    Route::post('packages/export', 'PackageReportsController@export');
    Route::post('orders/export', 'OrderController@export');
    Route::resource('third-party-orders', 'Reports\ThirdPartyOrderController');
    Route::resource('associate-orders', 'Reports\AssociateOrderController');
    Route::resource('imported-students', 'Reports\ImportedStudentController');
    Route::get('vaibhav-registration-details','VaibhavRegController@index');
});

Route::post('video-transcode-webhook', 'VideoController@transcodeWebhook');

Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
    Route::resource('categories', 'BlogCategoryController');
    Route::resource('tags', 'BlogTagController');
});


Route::middleware('auth')
    ->prefix('api')
    ->namespace('API')
    ->name('api.')
    ->group(function () {

        Route::resource('courses', 'CourseController')->only('index');
        Route::resource('levels', 'LevelController')->only('index');
        Route::resource('subjects', 'SubjectController')->only('index');
        Route::resource('chapters', 'ChapterController')->only('index');
        Route::resource('professors', 'ProfessorController')->only('index');
        Route::resource('languages', 'LanguageController')->only('index');
        Route::resource('countries', 'CountryController')->only('index');
        Route::resource('modules', 'ModuleController')->only('index');
        Route::resource('countries', 'CountryController')->only('index');
        Route::resource('states', 'StateController')->only('index');


        Route::get('videos/files', 'VideoController@files')->name('videos.files.index');
        Route::get('videos/folder', 'VideoController@folders')->name('videos.folder.index');
        Route::get('videos/group', 'VideoController@group');
        Route::post('s3videos/uploadS3', 'VideoControllerS3@uploadS3')->name('s3videos.uploadS3.index');
        Route::get('s3videos/files', 'VideoControllerS3@files')->name('s3videos.files.index');
        Route::get('s3videos/folder', 'VideoControllerS3@folders')->name('s3videos.folder.index');
        Route::post('s3videos/updateDuration/{id}', 'VideoControllerS3@updateDuration')->name('s3videos.updateDuration.index');
        Route::get('packages/group', 'PackageController@group');
        Route::get('packages/selected', 'PackageController@getSelected');

        Route::get('sync-packages', 'PackageController@syncPackages');
        Route::get('get-professors', 'PackageController@getProfessors');

        Route::get('packages/csv', 'PackageController@getCSV');
        Route::get('packages/verify', 'PackageController@verify');
        Route::get('orders/{id}/verify', 'OrderController@verify');
        Route::get('student-packages', 'OrderController@getStudentPackages');
        Route::get('sections/group', 'SectionController@group');

    });

Route::get('get-all-packages-table', 'Package\CustomizeController@getAllPackagesTable')->name('get-all-packages-table');
Route::get('get-all-videos-table', 'Package\CustomizeController@getAllVideosTable')->name('get-all-videos-table');

Route::get('sync-payments', 'API\PaymentController@syncPayments');
Route::get('sync-payment-receipt', 'API\PaymentController@syncPaymentReceipt');
Route::get('sync-payment-order-items', 'API\PaymentController@syncPaymentOrderItems');
Route::get('sync-order-items', 'API\PaymentController@syncOrderItems');
Route::get('sync-payment-cc-avenue-order-id', 'API\PaymentController@syncPaymentCCAvenueOrderID');
Route::get('sync-order-items-user-id', 'API\PaymentController@syncOrderItemsUserID');
Route::get('sync-prepaid-orders', 'API\PaymentController@syncPrepaidOrders');
Route::get('sync-order-items-type', 'API\PaymentController@syncOrderItemsType');
Route::get('sync-professor-revenues', 'API\PaymentController@syncProfessorRevenues');
Route::get('sync-payments-created-at', 'API\PaymentController@syncPaymentsCreatedAt');
Route::get('get-ca-final-full-course-packages', 'PackageExcelController@index');
Route::get('get-order-items', 'OrderItemController@index');
Route::get('update-order-items-expire-at', 'OrderItemController@updateExpireAt');
Route::get('update-package-reviews', 'PackageController@updateReviews');
Route::get('update-order-item-extension-from-package-extension', 'OrderItemController@updateExtention');
Route::get('/update-progress-percentage', 'OrderItemController@updateProgressPercentage');
Route::get('update-selling-amount-of-packages', 'PackageController@updateSellingAmount');
Route::resource('video-histories', 'VideoHistoryController')->only('index','store');
Route::get('videos/get-player/{id}', 'VideoController@getPlayer');

//new route add 
Route::get('/upload', 'ScormController@showForm');
Route::post('/upload', 'ScormController@upload')->name('scorm.upload');
Route::get('/view/{id}', 'ScormController@view')->middleware('auth');
Route::post('/scorm/progress/save', 'ScormController@saveProgress')->name('scorm.progress.save');

Route::get('/dashboard', 'UserDashboardController@index')->name('user.dashboard')->middleware('auth');
// web.php
Route::post('/course/progress/update', 'UserDashboardController@resumeupdate')->middleware('auth');

// routes/web.php
Route::get('/user/dashboard', 'UserDashboardController@userindex')->name('user.dashboard')->middleware('auth');

Route::post('/course/progress/save', 'CourseProgressController@save')->name('course.progress.save');
Route::get('/course/progress/get/{id}', 'CourseProgressController@get');



