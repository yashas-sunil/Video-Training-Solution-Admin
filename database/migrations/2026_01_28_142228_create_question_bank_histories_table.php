<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionBankHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_bank_histories', function (Blueprint $table) {
            $table->id();
                 $table->string('name', 88);
            $table->unsignedBigInteger('question_banks_id')->nullable();

            $table->string('file', 191)->nullable();
            $table->unsignedBigInteger('languages_id')->nullable();

            $table->string('message', 255)->nullable();

            $table->tinyInteger('qbstatus')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
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
        Schema::dropIfExists('question_bank_histories');
    }
}
