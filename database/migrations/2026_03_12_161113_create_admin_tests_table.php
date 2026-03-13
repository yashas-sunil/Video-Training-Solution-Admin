<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_test', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->string('course_id');
            $table->string('subject_id');
            $table->string('total_ques_count');
            $table->string('easy_count');
            $table->string('medium_count');
            $table->string('hard_count');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('admin_test');
    }
}
