<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubchaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subchapters', function (Blueprint $table) {
            $table->id();
              $table->string('name');
        $table->integer('subchapter_order')->nullable();
        $table->tinyInteger('status')->default(1);
        $table->unsignedBigInteger('subjects_id')->nullable();
        $table->unsignedBigInteger('course_id')->nullable();
        $table->unsignedBigInteger('level_id')->nullable();
        $table->unsignedBigInteger('chapter_id')->nullable();

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
        Schema::dropIfExists('subchapters');
    }
}
