<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('course_id');
        $table->unsignedBigInteger('level_id');
        $table->unsignedBigInteger('attempt_id')->nullable();

        $table->integer('score')->nullable();
        $table->integer('total_marks')->nullable();
        $table->decimal('overall_percentage', 5, 2)->nullable();

        $table->string('student_result')->nullable();  
        $table->tinyInteger('status')->default(1);  
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
        Schema::dropIfExists('goals');
    }
}
