<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_test_results', function (Blueprint $table) {
            $table->id();
            $table->string('test_id');
            $table->string('user_id');
            $table->integer('total_no_ques');
            $table->integer('answer_id');
            $table->integer('answer_selected');
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
        Schema::dropIfExists('admin_test_results');
    }
}
