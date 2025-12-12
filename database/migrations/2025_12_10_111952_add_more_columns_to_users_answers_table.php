<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreColumnsToUsersAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_answers', function (Blueprint $table) {
              $table->unsignedBigInteger('answers_id')->nullable()->after('id');
        $table->unsignedBigInteger('correct_answers_id')->nullable()->after('answers_id');

        $table->decimal('marks', 5, 2)->default(0)->after('correct_answers_id');
        $table->decimal('negative_marks', 5, 2)->default(0)->after('marks');

        $table->text('message')->nullable()->after('negative_marks');

        $table->integer('time_taken')->nullable()->after('message');

        $table->unsignedBigInteger('question_level_id')->nullable()->after('time_taken');

        $table->boolean('is_cumulative_question')
              ->default(0)
              ->after('question_level_id'); // 0 = No, 1 = Yes
        
        $table->tinyInteger('user_question_status')
              ->default(0)
              ->comment('0=pending, 1=correct, 2=wrong')
              ->after('is_cumulative_question');

        $table->unsignedBigInteger('chapters_questions_id')->nullable()->after('user_question_status');

        $table->unsignedBigInteger('submitted_quiz_id')->nullable()->after('chapters_questions_id');
        $table->unsignedBigInteger('submitted_objective_id')->nullable()->after('submitted_quiz_id');
        $table->unsignedBigInteger('submitted_block_id')->nullable()->after('submitted_objective_id');
        $table->unsignedBigInteger('submitted_championship_id')->nullable()->after('submitted_block_id');
        $table->unsignedBigInteger('submitted_tournament_id')->nullable()->after('submitted_championship_id');

        // $table->tinyInteger('status')->default(1)->after('submitted_tournament_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_answers', function (Blueprint $table) {
              $table->dropColumn([
            'answers_id',
            'correct_answers_id',
            'marks',
            'negative_marks',
            'message',
            'time_taken',
            'question_level_id',
            'is_cumulative_question',
            'user_question_status',
            'chapters_questions_id',
            'submitted_quiz_id',
            'submitted_objective_id',
            'submitted_block_id',
            'submitted_championship_id',
            'submitted_tournament_id',
            // 'status'
        ]);
        });
    }
}
