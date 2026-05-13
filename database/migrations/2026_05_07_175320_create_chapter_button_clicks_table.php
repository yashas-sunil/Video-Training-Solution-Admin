<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterButtonClicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_button_clicks', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('chapter_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('button_type'); // 'start', 'resume', 'completed'
            $table->integer('click_count')->default(0);
            $table->timestamp('last_clicked_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('scorm_packages')->onDelete('cascade');
            $table->unique(['user_id', 'chapter_id', 'button_type']);
            
            $table->index(['user_id', 'chapter_id']);
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chapter_button_clicks');
    }
}
