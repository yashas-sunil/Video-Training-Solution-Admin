<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamStartLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_start_logs', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('course_id')->nullable();
        $table->unsignedBigInteger('subject_id')->nullable();
        $table->unsignedBigInteger('chapter_id')->nullable();
        $table->unsignedBigInteger('test_id')->nullable();
        $table->integer('open_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_start_logs');
    }
}
