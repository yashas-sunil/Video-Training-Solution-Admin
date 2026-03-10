<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
    Route::post('get-chapterBy-subject', 'SubjectController@getChapterBySubjectId');
    Route::get('/subjects/{courseId}', 'ChapterController@getSubjectsByCourse');
    Route::post('/v1/student/register','StudentRegisterController@register');
    Route::post('/save-user-answers', 'FiltertQuestionController@store')->name('course.progress.save');
    Route::post('/launch', 'LaunchController@launch');
    Route::post('/v1/student/token', 'StudentTokenController@generate');

