<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('question_banks_id')->nullable()->after('id');
        $table->unsignedBigInteger('course_id')->nullable();
        $table->unsignedBigInteger('level_id')->nullable();
        $table->unsignedBigInteger('subchapter_id')->nullable();

        $table->unsignedBigInteger('difficult_levels_id')->nullable();
        $table->unsignedBigInteger('answer_types_id')->nullable();

        $table->string('correct_answer')->nullable();
        $table->string('tags')->nullable();

        $table->unsignedBigInteger('championship_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
              $table->dropColumn([
            'question_banks_id',
            'course_id',
            'level_id',
            'subchapter_id',
            'difficult_levels_id',
            'answer_types_id',
            'correct_answer',
            'tags',
            'championship_id'
        ]);
        });
    }
}
