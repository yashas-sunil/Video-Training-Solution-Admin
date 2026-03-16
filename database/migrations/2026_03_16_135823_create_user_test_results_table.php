<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_test_results', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('user_id');           
            $table->unsignedBigInteger('test_id');           
            $table->unsignedBigInteger('question_id');      
            $table->unsignedBigInteger('selected_option_id')->nullable(); 
            $table->tinyInteger('is_correct')->default(0);   
            $table->string('mode')->default('test');     
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
        Schema::dropIfExists('user_test_results');
    }
}
