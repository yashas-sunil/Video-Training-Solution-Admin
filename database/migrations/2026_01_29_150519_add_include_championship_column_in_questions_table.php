<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncludeChampionshipColumnInQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->tinyInteger('include_championship')->default(0);
            $table->mediumInteger('marks')->default(1);
            $table->mediumInteger('negative_marks')->default(1);
            $table->integer('question_types_id')->nullable();
            $table->string('source')->nullable();
            $table->unsignedBigInteger('types_id')->nullable();
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
            $table->dropColumn(['include_championship', 'marks','negative_marks', 'question_types_id', 'source', 'types_id']);
        });
    }
}
