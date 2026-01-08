<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lessons')) {
            Schema::create('lessons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chapter_id');
                $table->string('lesson_name');
                $table->integer('lesson_order')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('chapter_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}
