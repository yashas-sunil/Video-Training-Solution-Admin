<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
        $table->unsignedBigInteger('level_id');

        $table->string('name');
        $table->string('icon_name')->nullable();
        $table->date('exam_date')->nullable();

        $table->decimal('off_price', 10, 2)->nullable();
        $table->integer('score_target')->nullable();

        $table->tinyInteger('status')->default(1); 
        $table->integer('sort')->default(1);
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
        Schema::dropIfExists('attempts');
    }
}
